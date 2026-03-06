<?php

require "auth.php";
include "db.php";

$sql = "SELECT * FROM users ORDER BY score DESC";
$result = $conn->query($sql);

// Detect columns (so UI works across schema versions)
$cols = [];
$colRes = $conn->query("SHOW COLUMNS FROM users");
if($colRes){
    while($row = $colRes->fetch_assoc()){
        $cols[$row["Field"]] = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Portfolio Analyzer Dashboard</title>

<style>

body{
font-family: Arial, sans-serif;
background:#111827;
color:white;
padding:40px;
}

h1{
margin-bottom:30px;
}

table{
width:100%;
border-collapse:collapse;
background:#1f2937;
}

th,td{
padding:12px;
text-align:center;
border-bottom:1px solid #374151;
}

th{
background:#0f172a;
}

tr:hover{
background:#374151;
}

</style>

</head>

<body>

<h1>Portfolio Analyzer Dashboard</h1>

<table>

<tr>
<th>Name</th>
<th>Email</th>
<th>Gender</th>
<th>Role</th>
<?php if(isset($cols["company"])) echo "<th>Company</th>"; ?>
<?php if(isset($cols["company_role"])) echo "<th>Company Role</th>"; ?>
<th>Score</th>
<?php if(isset($cols["readiness_label"])) echo "<th>Readiness</th>"; ?>
<?php if(isset($cols["skill_match_percent"])) echo "<th>Skill Match %</th>"; ?>
<th>Projects</th>
<th>Experience</th>
<?php if(isset($cols["analysis_date"])) echo "<th>Analysis Date</th>"; ?>
<?php if(isset($cols["salary_expectation"])) echo "<th>Salary Expectation (LPA)</th>"; ?>
</tr>

<?php

if($result->num_rows > 0){

while($row = $result->fetch_assoc()){

echo "<tr>";

echo "<td>".$row["name"]."</td>";
echo "<td>".$row["email"]."</td>";
$gender = isset($row["gender"]) && $row["gender"] !== "" ? $row["gender"] : "N/A";
echo "<td>".$gender."</td>";
echo "<td>".$row["role"]."</td>";
if(isset($cols["company"])) echo "<td>".($row["company"] ?? "—")."</td>";
if(isset($cols["company_role"])) echo "<td>".($row["company_role"] ?? "—")."</td>";
echo "<td>".$row["score"]."</td>";
if(isset($cols["readiness_label"])) echo "<td>".($row["readiness_label"] ?? "—")."</td>";
if(isset($cols["skill_match_percent"])) echo "<td>".($row["skill_match_percent"] ?? "—")."</td>";
echo "<td>".$row["projects"]."</td>";

$exp = $row["experience_years"]." years ".$row["experience_months"]." months";

echo "<td>".$exp."</td>";
if(isset($cols["analysis_date"])) echo "<td>".($row["analysis_date"] ?? "—")."</td>";
if(isset($cols["salary_expectation"])) echo "<td>".($row["salary_expectation"] ? "₹".$row["salary_expectation"] : "—")."</td>";

echo "</tr>";

}

}

?>

</table>

</body>
</html>