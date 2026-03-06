<?php
require "db.php";

$role = $_GET['role'] ?? "";
$min_score = $_GET['min_score'] ?? "";

$sql = "
SELECT d.name, d.email, a.role, a.score, a.skill_match_percent
FROM analyses a
JOIN developers d ON a.developer_id = d.id
WHERE 1=1
";

if($role !== ""){
    $sql .= " AND a.role = '".$conn->real_escape_string($role)."'";
}

if($min_score !== ""){
    $sql .= " AND a.score >= ".intval($min_score);
}

$sql .= " ORDER BY a.score DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Candidate Search</title>
<link rel="stylesheet" href="style.css">
<style>
body{
    font-family: Arial, sans-serif;
    background:#f5f7fb;
    margin:0;
    padding:0;
}

.container{
    max-width:1000px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

h2{
    margin-top:0;
}

.search-form{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:25px;
}

.search-form label{
    font-size:14px;
    font-weight:600;
}

.search-form select,
.search-form input{
    padding:8px;
    border:1px solid #ccc;
    border-radius:6px;
}

.search-form button{
    padding:9px 16px;
    background:#4f46e5;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

.search-form button:hover{
    background:#4338ca;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#4f46e5;
    color:white;
    padding:10px;
    text-align:left;
}

td{
    padding:10px;
    border-bottom:1px solid #eee;
}

tr:hover{
    background:#f3f4f6;
}
</style>
</head>

<body>

<div class="container">

<h2>Search Developer Candidates</h2>

<form method="GET" class="search-form">

<div>
<label>Role</label><br>
<select name="role">
<option value="">All Roles</option>
<option value="Frontend Developer">Frontend Developer</option>
<option value="Backend Developer">Backend Developer</option>
<option value="Full Stack Developer">Full Stack Developer</option>
<option value="Data Scientist">Data Scientist</option>
</select>
</div>

<div>
<label>Minimum Score</label><br>
<input type="number" name="min_score" placeholder="e.g. 70">
</div>

<div style="align-self:flex-end;">
<button type="submit">Search</button>
</div>

</form>

<table>
<tr>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Score</th>
<th>Skill Match %</th>
</tr>

<?php if($result && $result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['role']); ?></td>
<td><?php echo htmlspecialchars($row['score']); ?></td>
<td><?php echo htmlspecialchars($row['skill_match_percent']); ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="5">No candidates found.</td>
</tr>
<?php endif; ?>

</table>

</div>

</body>
</html>