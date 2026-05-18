<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/AdminWorkoutsModel.php';

class AdminWorkoutsManager {
    private $adminWorkoutsModel;

    public function __construct() {
        $db = (new Workout_Database())->_ConnectDB();
        $this->adminWorkoutsModel = new AdminWorkoutsModel($db);
    }

    public function getWorkouts(): array {
        return $this->adminWorkoutsModel->getWorkouts();
    }

    public function getStats(): array {
        return $this->adminWorkoutsModel->getStats();
    }

    public function getWeeklyRows(): array {
        return $this->adminWorkoutsModel->getWeeklyRows();
    }

    public function getPopularRows(): array {
        return $this->adminWorkoutsModel->getPopularRows();
    }
}
