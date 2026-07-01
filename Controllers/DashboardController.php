<?php

require_once __DIR__ . '/../Models/Dashboard.php';

class DashboardController
{
    private $dashboard;

    public function __construct()
    {
        $this->dashboard = new Dashboard();
    }

    public function index()
    {
        return $this->dashboard->getDashboard();
    }
}