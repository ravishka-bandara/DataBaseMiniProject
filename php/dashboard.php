<?php

require_once "db.php";

header("Content-Type: application/json");

$data = [];


// Count Members
$result = $conn->query(
    "SELECT COUNT(*) AS total FROM members"
);

$data["members"] = $result->fetch_assoc()["total"];


// Count Plans
$result = $conn->query(
    "SELECT COUNT(*) AS total FROM membership_plans"
);

$data["plans"] = $result->fetch_assoc()["total"];


// Count Trainers
$result = $conn->query(
    "SELECT COUNT(*) AS total FROM trainers"
);

$data["trainers"] = $result->fetch_assoc()["total"];


// Count Payments
$result = $conn->query(
    "SELECT COUNT(*) AS total FROM payments"
);

$data["payments"] = $result->fetch_assoc()["total"];


echo json_encode($data);

?>