<?php
class AdminReportsModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDashboardStats() {
        try {
            $usersSql = "SELECT COUNT(*) AS total FROM tbl_users";
            $stmt = $this->conn->prepare($usersSql);
            $stmt->execute();
            $users = (int)$stmt->fetchColumn();

            $activeSql = "SELECT COUNT(*) AS total FROM tbl_users WHERE DATE(last_activity) = CURDATE()";
            $stmt = $this->conn->prepare($activeSql);
            $stmt->execute();
            $activeToday = (int)$stmt->fetchColumn();

            $sessionsSql = "SELECT COUNT(*) AS total FROM tbl_user_workout_sessions";
            $stmt = $this->conn->prepare($sessionsSql);
            $stmt->execute();
            $sessions = (int)$stmt->fetchColumn();

            $minutesSql = "SELECT COALESCE(SUM(duration_seconds),0) AS total_seconds FROM tbl_user_workout_sessions";
            $stmt = $this->conn->prepare($minutesSql);
            $stmt->execute();
            $totalSeconds = (int)$stmt->fetchColumn();

            return [
                'users' => $users,
                'activeToday' => $activeToday,
                'sessions' => $sessions,
                'minutes' => (int)floor($totalSeconds / 60),
            ];
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [
                'users' => 0,
                'activeToday' => 0,
                'sessions' => 0,
                'minutes' => 0
            ];
        }
    }

    public function getActivityRows() {
        try {
            $sqlQuery = "SELECT DATE(date_completed) AS label, FLOOR(COALESCE(SUM(duration_seconds),0) / 60) AS total
                         FROM tbl_user_workout_sessions
                         WHERE date_completed >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
                         GROUP BY DATE(date_completed)
                         ORDER BY label";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [];
        }
    }

    public function getRegistrationRows() {
        try {
            $sqlQuery = "SELECT DATE(created_At) AS label, COUNT(*) AS total
                         FROM tbl_users
                         WHERE created_At >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
                         GROUP BY DATE(created_At)
                         ORDER BY label";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [];
        }
    }

    public function getGoalRows() {
        try {
            $sqlQuery = "SELECT COALESCE(NULLIF(status,''),'Unassigned') AS label, COUNT(*) AS total
                         FROM tbl_goals
                         GROUP BY label
                         ORDER BY total DESC";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [];
        }
    }

    public function getMuscleRows() {
        try {
            $sqlQuery = "SELECT COALESCE(e.muscle, 'Custom') AS label, COUNT(*) AS total
                         FROM tbl_workout_exercises we
                         LEFT JOIN tbl_exercises e ON we.exerciseID = e.exerciseID
                         GROUP BY label
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

    public function getTopUsers() {
        try {
            $sqlQuery = "SELECT u.username, COUNT(s.sessionID) AS sessions, FLOOR(COALESCE(SUM(s.duration_seconds),0)/60) AS minutes
                         FROM tbl_users u
                         LEFT JOIN tbl_user_workout_sessions s ON u.userID = s.userID
                         GROUP BY u.userID
                         ORDER BY sessions DESC, minutes DESC
                         LIMIT 8";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [];
        }
    }
}
?>
