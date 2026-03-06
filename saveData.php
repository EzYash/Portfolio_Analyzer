<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include "db.php";
session_start();
$user_id = $_SESSION["user_id"] ?? null;

if(!isset($conn)){
    die("Database connection variable \$conn not found. Check db.php");
}

if($conn->connect_error){
    die("Database connection failed: " . $conn->connect_error);
}


$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    die("No JSON data received from frontend");
}

$role = isset($data["role"]) ? (is_array($data["role"]) ? implode(", ", $data["role"]) : $data["role"]) : "";
$projects = isset($data["projects"]) ? (is_array($data["projects"]) ? implode(", ", $data["projects"]) : $data["projects"]) : "";
$name = $data["name"] ?? "";
$email = $data["email"] ?? "";
$gender = $data["gender"] ?? "";
$company = $data["company"] ?? "";
$company_role = $data["company_role"] ?? "";
$score = $data["score"] ?? 0;
$skill_match_percent = $data["skill_match_percent"] ?? 0;
$readiness_label = $data["readiness_label"] ?? "";
$matched_skills = $data["matched_skills"] ?? [];
$missing_skills = $data["missing_skills"] ?? [];
$analysis_date = date("Y-m-d");
$experience_years = $data["experience_years"] ?? 0;
$experience_months = $data["experience_months"] ?? 0;
$experience_total = $data["experience_total"] ?? 0;
$salary_expectation = $data["salary_expectation"] ?? 0;

$developer_id = $user_id;
if(!$developer_id){
    die("User not logged in");
}

/* Ensure developer exists to satisfy foreign key constraint */
$checkDev = $conn->prepare("SELECT id FROM developers WHERE id = ?");
$checkDev->bind_param("i", $developer_id);
$checkDev->execute();
$resDev = $checkDev->get_result();

if($resDev->num_rows === 0){
    $insertDev = $conn->prepare("INSERT INTO developers (id, name, email) VALUES (?, ?, ?)");
    $insertDev->bind_param("iss", $developer_id, $name, $email);
    if(!$insertDev->execute()){
        die("Failed to create developer record: " . $insertDev->error);
    }
    $insertDev->close();
}
$checkDev->close();

// Prepare SQL for analyses table
$sql = "INSERT INTO analyses (
developer_id,
role,
company,
company_role,
score,
projects,
skill_match_percent,
readiness_label,
matched_skills,
missing_skills,
experience_years,
experience_months,
experience_total,
salary_expectation,
analysis_date
) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);

if(!$stmt){
    die("Prepare failed: " . $conn->error);
}

$matched_skills_json = json_encode($matched_skills);
$missing_skills_json = json_encode($missing_skills);

$stmt->bind_param(
"isssisisssiidds",
$developer_id,
$role,
$company,
$company_role,
$score,
$projects,
$skill_match_percent,
$readiness_label,
$matched_skills_json,
$missing_skills_json,
$experience_years,
$experience_months,
$experience_total,
$salary_expectation,
$analysis_date
);

if($stmt->execute()){
    echo json_encode(["status"=>"success"]);
}else{
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}

$stmt->close();

?>