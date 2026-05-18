<?php
class ActivityLogModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function record($userID, $activityType, $description = '', $status = 'success', $workoutID = null, $exerciseID = null) {
        try {
            $this->conn->beginTransaction(); // to make sure auto-commit is not immediate

            $system = $this->conn->prepare(
                "INSERT INTO tbl_activity_logs (userID, action, date_of_action,created_At,updated_At)
                 VALUES (:userID, :action, NOW(),NOW(),NOW())"
            );
            // skips the bindParam and just key map the values to the values
            $system->execute([
                ':userID' => (int)$userID,
                ':action' => $activityType
            ]);

            $user = $this->conn->prepare(
                "INSERT INTO tbl_user_activity_logs
                    (userID, workoutID, exerciseID, activity_type, description, status, activity_time,created_At,updated_At)
                 VALUES
                    (:userID, :workoutID, :exerciseID, :activity_type, :description, :status, NOW(), NOW(), NOW())"
            );
            $user->execute([
                ':userID' => (int)$userID,
                ':workoutID' => $workoutID,
                ':exerciseID' => $exerciseID,
                ':activity_type' => $activityType,
                ':description' => $description,
                ':status' => $status
            ]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }

    public function getRecentLogs($limit = 200) {
        try {
            $sql = "
                SELECT * FROM (
                    SELECT CONCAT('sys-', l.logID) AS id, 'System' AS source, u.username, l.action AS activity_type,
                           NULL AS workout_name, NULL AS exercise_name, l.action AS description, NULL AS status,
                           l.date_of_action AS activity_time
                    FROM tbl_activity_logs l
                    LEFT JOIN tbl_users u ON l.userID = u.userID
                    UNION ALL
                    SELECT CONCAT('user-', al.Activity_LogID) AS id, 'User' AS source, u.username, al.activity_type,
                           w.name AS workout_name, e.name AS exercise_name, al.description, al.status,
                           al.activity_time
                    FROM tbl_user_activity_logs al
                    LEFT JOIN tbl_users u ON al.userID = u.userID
                    LEFT JOIN tbl_workouts w ON al.workoutID = w.workoutID
                    LEFT JOIN tbl_exercises e ON al.exerciseID = e.exerciseID
                ) merged_logs
                ORDER BY activity_time DESC
                LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getStats() {
        try {
            $allSql = "SELECT (SELECT COUNT(*) FROM tbl_activity_logs) + (SELECT COUNT(*) FROM tbl_user_activity_logs) AS total";
            $stmt = $this->conn->prepare($allSql);
            $stmt->execute();
            $all = (int)$stmt->fetchColumn();

            $todaySql = "SELECT (SELECT COUNT(*) FROM tbl_activity_logs WHERE DATE(date_of_action)=CURDATE()) + (SELECT COUNT(*) FROM tbl_user_activity_logs WHERE DATE(activity_time)=CURDATE()) AS total";
            $stmt = $this->conn->prepare($todaySql);
            $stmt->execute();
            $today = (int)$stmt->fetchColumn();

            $workoutSql = "SELECT COUNT(*) AS total FROM tbl_user_activity_logs WHERE workoutID IS NOT NULL";
            $stmt = $this->conn->prepare($workoutSql);
            $stmt->execute();
            $workout = (int)$stmt->fetchColumn();

            $exerciseSql = "SELECT COUNT(*) AS total FROM tbl_user_activity_logs WHERE exerciseID IS NOT NULL";
            $stmt = $this->conn->prepare($exerciseSql);
            $stmt->execute();
            $exercise = (int)$stmt->fetchColumn();

            return [
                'all' => $all,
                'today' => $today,
                'workout' => $workout,
                'exercise' => $exercise
            ];
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [
                'all' => 0,
                'today' => 0,
                'workout' => 0,
                'exercise' => 0
            ];
        }
    }

    public function getTypeRows() {
        try {
            $sqlQuery = "SELECT activity_type AS label, COUNT(*) AS total
                         FROM tbl_user_activity_logs
                         GROUP BY activity_type
                         ORDER BY total DESC
                         LIMIT 8";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [];
        }
    }

    public function getDailyRows() {
        try {
            $sqlQuery = "SELECT DATE(activity_time) AS label, COUNT(*) AS total
                         FROM tbl_user_activity_logs
                         WHERE activity_time >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
                         GROUP BY DATE(activity_time)
                         ORDER BY label";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [];
        }
    }

    public function getPlatformActivitySeries($type = 'weekly') {
        try {
            if ($type === 'monthly') {
                $sqlQuery = "SELECT DATE(activity_time) AS day, COUNT(*) AS total
                             FROM tbl_user_activity_logs
                             WHERE activity_time >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                               AND activity_time < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)
                             GROUP BY DATE(activity_time)
                             ORDER BY day";
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $this->formatDateSeries($rows, 'monthly');
            }

            $sqlQuery = "SELECT DATE(activity_time) AS day, COUNT(*) AS total
                         FROM tbl_user_activity_logs
                         WHERE activity_time >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                         GROUP BY DATE(activity_time)
                         ORDER BY day";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->formatDateSeries($rows, 'weekly');
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return ['labels' => [], 'values' => []];
        }
    }

    private function formatDateSeries(array $rows, $type) {
        $mapped = [];
        foreach ($rows as $row) {
            $mapped[$row['day']] = (int)$row['total'];
        }

        $labels = [];
        $values = [];

        if ($type === 'monthly') {
            $daysInMonth = (int)date('t');
            $year = date('Y');
            $month = date('m');

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = "$year-$month-" . str_pad((string)$day, 2, "0", STR_PAD_LEFT);
                $labels[] = date('M d', strtotime($date));
                $values[] = $mapped[$date] ?? 0;
            }

            return ['labels' => $labels, 'values' => $values];
        }

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('D', strtotime($date));
            $values[] = $mapped[$date] ?? 0;
        }

        return ['labels' => $labels, 'values' => $values];
    }

}
?>
