<?php

require_once "db.php";

header("Content-Type: application/json");

$action = $_GET["action"] ?? "get";


// GET / SEARCH
if ($action === "get") {

    $search = $_GET["search"] ?? "";

    $stmt = $conn->prepare(
        "SELECT * FROM trainers
         WHERE trainer_name LIKE ?
         OR phone LIKE ?
         OR specialization LIKE ?
         ORDER BY trainer_id DESC"
    );

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param(
        "sss",
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $trainers = [];

    while ($row = $result->fetch_assoc()) {
        $trainers[] = $row;
    }

    echo json_encode($trainers);
    exit;
}


// ADD
if ($action === "add") {

    $data = json_decode(file_get_contents("php://input"), true);

    $trainer_name = trim($data["trainer_name"] ?? "");
    $phone = trim($data["phone"] ?? "");
    $email = trim($data["email"] ?? "");
    $specialization = trim($data["specialization"] ?? "");

    if ($trainer_name === "") {

        echo json_encode([
            "success" => false,
            "message" => "Trainer name is required."
        ]);

        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO trainers
        (trainer_name, phone, email, specialization)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssss",
        $trainer_name,
        $phone,
        $email,
        $specialization
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Trainer added successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to add trainer."
        ]);
    }

    exit;
}


// UPDATE
if ($action === "update") {

    $data = json_decode(file_get_contents("php://input"), true);

    $trainer_id = intval($data["trainer_id"] ?? 0);
    $trainer_name = trim($data["trainer_name"] ?? "");
    $phone = trim($data["phone"] ?? "");
    $email = trim($data["email"] ?? "");
    $specialization = trim($data["specialization"] ?? "");

    if ($trainer_id <= 0 || $trainer_name === "") {

        echo json_encode([
            "success" => false,
            "message" => "Invalid trainer details."
        ]);

        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE trainers
         SET trainer_name = ?,
             phone = ?,
             email = ?,
             specialization = ?
         WHERE trainer_id = ?"
    );

    $stmt->bind_param(
        "ssssi",
        $trainer_name,
        $phone,
        $email,
        $specialization,
        $trainer_id
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Trainer updated successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to update trainer."
        ]);
    }

    exit;
}


// DELETE
if ($action === "delete") {

    $trainer_id = intval($_GET["id"] ?? 0);

    if ($trainer_id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid trainer ID."
        ]);

        exit;
    }

    $stmt = $conn->prepare(
        "DELETE FROM trainers
         WHERE trainer_id = ?"
    );

    $stmt->bind_param("i", $trainer_id);

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Trainer deleted successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to delete trainer."
        ]);
    }

    exit;
}


echo json_encode([
    "success" => false,
    "message" => "Invalid action."
]);

?>