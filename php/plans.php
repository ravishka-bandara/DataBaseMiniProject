<?php

require_once "db.php";

header("Content-Type: application/json");

$action = $_GET["action"] ?? "get";


// GET / SEARCH
if ($action === "get") {

    $search = $_GET["search"] ?? "";

    $stmt = $conn->prepare(
        "SELECT * FROM membership_plans
         WHERE plan_name LIKE ?
         ORDER BY plan_id DESC"
    );

    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();

    $result = $stmt->get_result();

    $plans = [];

    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }

    echo json_encode($plans);
    exit;
}


// ADD
if ($action === "add") {

    $data = json_decode(file_get_contents("php://input"), true);

    $plan_name = trim($data["plan_name"] ?? "");
    $duration_months = intval($data["duration_months"] ?? 0);
    $price = floatval($data["price"] ?? 0);
    $description = trim($data["description"] ?? "");

    if ($plan_name === "" || $duration_months <= 0 || $price < 0) {
        echo json_encode([
            "success" => false,
            "message" => "Please enter valid plan details."
        ]);
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO membership_plans
        (plan_name, duration_months, price, description)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sids",
        $plan_name,
        $duration_months,
        $price,
        $description
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Plan added successfully."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to add plan."
        ]);
    }

    exit;
}


// UPDATE
if ($action === "update") {

    $data = json_decode(file_get_contents("php://input"), true);

    $plan_id = intval($data["plan_id"] ?? 0);
    $plan_name = trim($data["plan_name"] ?? "");
    $duration_months = intval($data["duration_months"] ?? 0);
    $price = floatval($data["price"] ?? 0);
    $description = trim($data["description"] ?? "");

    if ($plan_id <= 0 || $plan_name === "" || $duration_months <= 0 || $price < 0) {
        echo json_encode([
            "success" => false,
            "message" => "Please enter valid plan details."
        ]);
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE membership_plans
         SET plan_name = ?,
             duration_months = ?,
             price = ?,
             description = ?
         WHERE plan_id = ?"
    );

    $stmt->bind_param(
        "sidsi",
        $plan_name,
        $duration_months,
        $price,
        $description,
        $plan_id
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Plan updated successfully."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to update plan."
        ]);
    }

    exit;
}


// DELETE
if ($action === "delete") {

    $plan_id = intval($_GET["id"] ?? 0);

    if ($plan_id <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid plan ID."
        ]);
        exit;
    }

    $stmt = $conn->prepare(
        "DELETE FROM membership_plans
         WHERE plan_id = ?"
    );

    $stmt->bind_param("i", $plan_id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Plan deleted successfully."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to delete plan."
        ]);
    }

    exit;
}


echo json_encode([
    "success" => false,
    "message" => "Invalid action."
]);

?>