<?php

require_once "db.php";

header("Content-Type: application/json");

$action = $_GET["action"] ?? "";


// =========================
// GET MEMBERS
// =========================

if ($action == "get") {

    $search = $_GET["search"] ?? "";

    $sql = "SELECT 
                m.member_id,
                m.full_name,
                m.gender,
                m.date_of_birth,
                m.phone,
                m.email,
                m.address,
                m.join_date,
                m.plan_id,
                m.trainer_id,
                p.plan_name,
                t.trainer_name
            FROM members m
            LEFT JOIN membership_plans p
                ON m.plan_id = p.plan_id
            LEFT JOIN trainers t
                ON m.trainer_id = t.trainer_id
            WHERE m.full_name LIKE ?
               OR m.phone LIKE ?
            ORDER BY m.member_id DESC";

    $stmt = $conn->prepare($sql);

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param("ss", $searchTerm, $searchTerm);

    $stmt->execute();

    $result = $stmt->get_result();

    $members = [];

    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }

    echo json_encode($members);

    exit;
}


// =========================
// ADD MEMBER
// =========================

if ($action == "add") {

    $data = json_decode(file_get_contents("php://input"), true);

    $full_name = trim($data["full_name"] ?? "");
    $gender = $data["gender"] ?? "";
    $date_of_birth = $data["date_of_birth"] ?? "";
    $phone = trim($data["phone"] ?? "");
    $email = trim($data["email"] ?? "");
    $address = trim($data["address"] ?? "");
    $join_date = $data["join_date"] ?? "";
    $plan_id = $data["plan_id"] ?? null;
    $trainer_id = $data["trainer_id"] ?? null;


    if ($full_name == "" || $gender == "" || $date_of_birth == "" ||
        $phone == "" || $join_date == "" || !$plan_id) {

        echo json_encode([
            "success" => false,
            "message" => "Please fill all required fields."
        ]);

        exit;
    }


    $sql = "INSERT INTO members
            (full_name, gender, date_of_birth, phone, email,
             address, join_date, plan_id, trainer_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssssii",
        $full_name,
        $gender,
        $date_of_birth,
        $phone,
        $email,
        $address,
        $join_date,
        $plan_id,
        $trainer_id
    );


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Member added successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to add member."
        ]);
    }

    exit;
}


// =========================
// UPDATE MEMBER
// =========================

if ($action == "update") {

    $data = json_decode(file_get_contents("php://input"), true);

    $member_id = $data["member_id"] ?? 0;

    $full_name = trim($data["full_name"] ?? "");
    $gender = $data["gender"] ?? "";
    $date_of_birth = $data["date_of_birth"] ?? "";
    $phone = trim($data["phone"] ?? "");
    $email = trim($data["email"] ?? "");
    $address = trim($data["address"] ?? "");
    $join_date = $data["join_date"] ?? "";
    $plan_id = $data["plan_id"] ?? null;
    $trainer_id = $data["trainer_id"] ?? null;


    if (!$member_id || $full_name == "" || $gender == "" ||
        $date_of_birth == "" || $phone == "" || $join_date == "" || !$plan_id) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid member information."
        ]);

        exit;
    }


    $sql = "UPDATE members SET
                full_name = ?,
                gender = ?,
                date_of_birth = ?,
                phone = ?,
                email = ?,
                address = ?,
                join_date = ?,
                plan_id = ?,
                trainer_id = ?
            WHERE member_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssssiii",
        $full_name,
        $gender,
        $date_of_birth,
        $phone,
        $email,
        $address,
        $join_date,
        $plan_id,
        $trainer_id,
        $member_id
    );


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Member updated successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to update member."
        ]);
    }

    exit;
}


// =========================
// DELETE MEMBER
// =========================

if ($action == "delete") {

    $member_id = $_GET["id"] ?? 0;

    if (!$member_id) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid member ID."
        ]);

        exit;
    }


    $sql = "DELETE FROM members WHERE member_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $member_id);


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Member deleted successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Failed to delete member."
        ]);
    }

    exit;
}


// =========================
// GET MEMBERSHIP PLANS
// =========================

if ($action == "plans") {

    $result = $conn->query(
        "SELECT plan_id, plan_name 
         FROM membership_plans
         ORDER BY plan_name"
    );

    $plans = [];

    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }

    echo json_encode($plans);

    exit;
}


// =========================
// GET TRAINERS
// =========================

if ($action == "trainers") {

    $result = $conn->query(
        "SELECT trainer_id, trainer_name
         FROM trainers
         ORDER BY trainer_name"
    );

    $trainers = [];

    while ($row = $result->fetch_assoc()) {
        $trainers[] = $row;
    }

    echo json_encode($trainers);

    exit;
}


echo json_encode([
    "success" => false,
    "message" => "Invalid action."
]);

?>