<?php

require_once "db.php";

header("Content-Type: application/json");

$action = $_GET["action"] ?? "get";


// GET / SEARCH
if ($action === "get") {

    $search = $_GET["search"] ?? "";

    $stmt = $conn->prepare(
        "SELECT 
            a.attendance_id,
            a.member_id,
            m.full_name,
            a.attendance_date,
            a.check_in_time,
            a.check_out_time
         FROM attendance a
         INNER JOIN members m
            ON a.member_id = m.member_id
         WHERE m.full_name LIKE ?
         ORDER BY a.attendance_id DESC"
    );

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();

    $result = $stmt->get_result();

    $attendance = [];

    while ($row = $result->fetch_assoc()) {
        $attendance[] = $row;
    }

    echo json_encode($attendance);
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
    $attendance_date = $data["attendance_date"] ?? "";
    $check_in_time = $data["check_in_time"] ?? "";
    $check_out_time = $data["check_out_time"] ?? null;

    if (
        $member_id <= 0 ||
        $attendance_date === "" ||
        $check_in_time === ""
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Please enter all required details."
        ]);
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO attendance
        (member_id, attendance_date, check_in_time, check_out_time)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "isss",
        $member_id,
        $attendance_date,
        $check_in_time,
        $check_out_time
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Attendance added successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to add attendance."
        ]);
    }

    exit;
}


// UPDATE
if ($action === "update") {

    $data = json_decode(file_get_contents("php://input"), true);

    $attendance_id = intval($data["attendance_id"] ?? 0);
    $member_id = intval($data["member_id"] ?? 0);
    $attendance_date = $data["attendance_date"] ?? "";
    $check_in_time = $data["check_in_time"] ?? "";
    $check_out_time = $data["check_out_time"] ?? null;

    if (
        $attendance_id <= 0 ||
        $member_id <= 0 ||
        $attendance_date === "" ||
        $check_in_time === ""
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Please enter all required details."
        ]);
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE attendance
         SET member_id = ?,
             attendance_date = ?,
             check_in_time = ?,
             check_out_time = ?
         WHERE attendance_id = ?"
    );

    $stmt->bind_param(
        "isssi",
        $member_id,
        $attendance_date,
        $check_in_time,
        $check_out_time,
        $attendance_id
    );

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Attendance updated successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to update attendance."
        ]);
    }

    exit;
}


// DELETE
if ($action === "delete") {

    $attendance_id = intval($_GET["id"] ?? 0);

    if ($attendance_id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid attendance ID."
        ]);

        exit;
    }

    $stmt = $conn->prepare(
        "DELETE FROM attendance
         WHERE attendance_id = ?"
    );

    $stmt->bind_param("i", $attendance_id);

    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Attendance deleted successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to delete attendance."
        ]);
    }

    exit;
}


echo json_encode([
    "success" => false,
    "message" => "Invalid action."
]);

?>