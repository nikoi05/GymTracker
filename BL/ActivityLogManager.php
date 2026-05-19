<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ActivityLogModel.php';

class ActivityLogManager {
    private $activityLogModel;

    public function __construct() {
        $database = new Workout_Database();
        $db = $database->_ConnectDB();
        $this->activityLogModel = new ActivityLogModel($db);
    }

    public function record($userID, $activityType, $description = '', $status = 'success', $workoutID = null, $exerciseID = null) {
        if (!$userID) {
            return false;
        }
        return $this->activityLogModel->record($userID, $activityType, $description, $status, $workoutID, $exerciseID);
    }

    public function getRecentLogs($limit = 200) {
        return $this->activityLogModel->getRecentLogs($limit);
    }

    public function getStats() {
        return $this->activityLogModel->getStats();
    }

    public function getTypeRows() {
        return $this->activityLogModel->getTypeRows();
    }

    public function getDailyRows() {
        return $this->activityLogModel->getDailyRows();
    }

    public function getPlatformActivitySeries($type = 'weekly') {
        return $this->activityLogModel->getPlatformActivitySeries($type);
    }
}
?>
