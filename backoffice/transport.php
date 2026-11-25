<?php
session_start();
require_once __DIR__ . '/../transport/controllers/VehicleController.php';
(new VehicleController())->backOffice();
