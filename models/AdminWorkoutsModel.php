<?php
class AdminWorkoutsModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getWorkouts() {
        try {
            $sqlQuery = "SELECT w.workoutID, w.name, w.workout_description, w.created_At, w.updated_At,
                                u.username, u.email,
                                COUNT(DISTINCT we.workout_exercises_ID) AS exercise_count,
                                COALESCE(SUM(we.sets), 0) AS planned_sets,
                                COALESCE(SUM(we.durationInMinutes), 0) AS planned_minutes,
                                COUNT(DISTINCT s.sessionID) AS session_count,
                                COALESCE(SUM(CASE WHEN s.status = 'completed' THEN 1 ELSE 0 END), 0) AS completed_count,
                                COALESCE(SUM(s.duration_seconds), 0) AS duration_seconds
                         FROM tbl_workouts w
                         LEFT JOIN tbl_users u ON w.userID = u.userID
                         LEFT JOIN tbl_workout_exercises we ON w.workoutID = we.workoutId
                         LEFT JOIN tbl_user_workout_sessions s ON w.workoutID = s.workoutID
                         GROUP BY w.workoutID
                         ORDER BY w.created_At DESC, w.workoutID DESC";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [];
        }
    }

    public function getStats() {
        try {
            $workoutsSql = "SELECT COUNT(*) AS total FROM tbl_workouts";
            $stmt = $this->conn->prepare($workoutsSql);
            $stmt->execute();
            $workouts = (int)$stmt->fetchColumn();

            $assignedSql = "SELECT COUNT(*) AS total FROM tbl_workout_exercises";
            $stmt = $this->conn->prepare($assignedSql);
            $stmt->execute();
            $assignedExercises = (int)$stmt->fetchColumn();

            $sessionsSql = "SELECT COUNT(*) AS total FROM tbl_user_workout_sessions";
            $stmt = $this->conn->prepare($sessionsSql);
            $stmt->execute();
            $sessions = (int)$stmt->fetchColumn();

            $completedSql = "SELECT COUNT(*) AS total FROM tbl_user_workout_sessions WHERE status = 'completed'";
            $stmt = $this->conn->prepare($completedSql);
            $stmt->execute();
            $completed = (int)$stmt->fetchColumn();

            return [
                'workouts' => $workouts,
                'assignedExercises' => $assignedExercises,
                'sessions' => $sessions,
                'completed' => $completed
            ];
        } catch (PDOException $e) {
            echo "Error" . $e->getMessage();
            return [
                'workouts' => 0,
                'assignedExercises' => 0,
                'sessions' => 0,
                'completed' => 0
            ];
        }
    }

    public function getWeeklyRows() {
        try {
            $sqlQuery = "SELECT DATE(created_At) AS label, COUNT(*) AS total
                         FROM tbl_workouts
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

    public function getPopularRows() {
        try {
            $sqlQuery = "SELECT w.name AS label, COUNT(s.sessionID) AS total
                         FROM tbl_workouts w
                         LEFT JOIN tbl_user_workout_sessions s ON w.workoutID = s.workoutID
                         GROUP BY w.workoutID
                         ORDER BY total DESC, w.created_At DESC
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
