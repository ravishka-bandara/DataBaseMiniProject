<?php

require_once "db.php";

header("Content-Type: application/json");

$action = $_GET["action"] ?? "get";


// GET / SEARCH
if ($action === "get") {

    $search = $_GET["search"] ?? "";

    $stmt = $conn->prepare(
        "SELECT
            p.payment_id,
            p.member_id,
            m.full_name,
            p.payment_date,
            p.amount,
            p.payment_method,
            p.description
         FROM payments p
         INNER JOIN members m
            ON p.member_id = m.member_id
         WHERE m.full_name LIKE ?
         ORDER BY p.payment_id DESC"
    );

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();

    $result = $stmt->get_result();

    $payments = [];

    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }

    echo json_encode($payments);
    exit;
}


// MEMBERS
if ($action === "members") {

    $result = $conn->query(
        "SELECT member_id, full_name
         FROM members
         ORDER BY full_name ASC"
    );

    $members = [];

    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }

    echo json_encode($members);
    exit;
}


// ADD
if ($action === "add") {

    $data = json_decode(file_get_contents("php://input"), true);

    $member_id = intval($data["member_id"] ?? 0);
    $payment_date = $data["payment_date"] ?? "";
    $amount = floatval($data["amount"] ?? 0);
    $payment_method = trim($data["payment_method"] ?? "");
    $description = trim($data["description"] ?? "");

    if (
        $member_id <= 0 ||
        $payment_date === "" ||
        $amount <= 0 ||
        $payment_method === ""
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Please enter valid payment details."
        ]);
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO payments
        (member_id, payment_date, amount, payment_method, description)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "isdss",
        $member_id,
        $payment_date,
        $amount,
        $payment_method,
        $description
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Payment added successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to add payment."
        ]);
    }

    exit;
}


// UPDATE
if ($action === "update") {

    $data = json_decode(file_get_contents("php://input"), true);

    $payment_id = intval($data["payment_id"] ?? 0);
    $member_id = intval($data["member_id"] ?? 0);
    $payment_date = $data["payment_date"] ?? "";
    $amount = floatval($data["amount"] ?? 0);
    $payment_method = trim($data["payment_method"] ?? "");
    $description = trim($data["description"] ?? "");

    if (
        $payment_id <= 0 ||
        $member_id <= 0 ||
        $payment_date === "" ||
        $amount <= 0 ||
        $payment_method === ""
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Please enter valid payment details."
        ]);
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE payments
         SET member_id = ?,
             payment_date = ?,
             amount = ?,
             payment_method = ?,
             description = ?
         WHERE payment_id = ?"
    );

    $stmt->bind_param(
        "isdssi",
        $member_id,
        $payment_date,
        $amount,
        $payment_method,
        $description,
        $payment_id
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Payment updated successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to update payment."
        ]);
    }

    exit;
}


// DELETE
if ($action === "delete") {

    $payment_id = intval($_GET["id"] ?? 0);

    if ($payment_id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid payment ID."
        ]);

        exit;
    }

    $stmt = $conn->prepare(
        "DELETE FROM payments
         WHERE payment_id = ?"
    );

    $stmt->bind_param("i", $payment_id);

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Payment deleted successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to delete payment."
        ]);
    }

    exit;
}


echo json_encode([
    "success" => false,
    "message" => "Invalid action."
]);

?>