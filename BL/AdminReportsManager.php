<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/AdminReportsModel.php';

class AdminReportsManager {
    private $adminReportsModel;

    public function __construct() {
        $db = (new Workout_Database())->_ConnectDB();
        $this->adminReportsModel = new AdminReportsModel($db);
    }

    public function getDashboardStats(): array {
        return $this->adminReportsModel->getDashboardStats();
    }

    public function getActivityRows(): array {
        return $this->adminReportsModel->getActivityRows();
    }

    public function getRegistrationRows(): array {
        return $this->adminReportsModel->getRegistrationRows();
    }

    public function getGoalRows(): array {
        return $this->adminReportsModel->getGoalRows();
    }

    public function getMuscleRows(): array {
        return $this->adminReportsModel->getMuscleRows();
    }

    public function getTopUsers(): array {
        return $this->adminReportsModel->getTopUsers();
    }
}
