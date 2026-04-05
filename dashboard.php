<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "auth.php";
include "db.php";

// 🔍 Search input
$search = $_GET['search'] ?? "";

// 🔥 Updated SQL with search
$sql = "
    SELECT 
        d.name, d.email,
        a.role, a.company, a.company_role,
        a.specialty, a.education,
        a.score, a.readiness_label, a.skill_match_percent,
        a.projects, a.experience_years, a.experience_months,
        a.analysis_date, a.salary_expectation
    FROM analyses a
    JOIN developers d ON a.developer_id = d.id
";

if (!empty($search)) {
    $searchSafe = $conn->real_escape_string($search);
    $sql .= " WHERE d.name LIKE '%$searchSafe%'";
}

$sql .= " ORDER BY a.score DESC, a.analysis_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portfolio Analyzer – Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;}

body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(135deg,#0b1220,#111827);
    color:#e2e8f0;
}

.topbar{
    position:sticky;top:0;
    padding:14px 24px;
    background:#0b1220;
    display:flex;justify-content:space-between;
}

.topbar a{color:white;text-decoration:none;margin-right:10px;}

.page{max-width:1400px;margin:auto;padding:30px;}

.page-header h1{font-size:28px;}

.search-box{
    margin:20px 0;
}

.search-box input{
    padding:10px;
    border-radius:8px;
    border:1px solid #1f2937;
    background:#0b1220;
    color:white;
}

.search-box button{
    padding:10px 15px;
    border:none;
    border-radius:8px;
    background:#6366f1;
    color:white;
    cursor:pointer;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:10px;
    border-bottom:1px solid #1f2937;
}

th{
    background:#111827;
}

.score-cell{color:#818cf8;font-weight:bold;}

.badge{padding:4px 10px;border-radius:10px;}
.good{color:#22c55e;}
.warn{color:#facc15;}
.bad{color:#ef4444;}

.empty-state{text-align:center;padding:40px;}
</style>
</head>

<body>

<div class="topbar">
    <div>Portfolio Analyzer</div>
    <div>
        <a href="index.php">Analyzer</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="history.php">History</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="page">

<div class="page-header">
<h1>Portfolio Analysis Overview</h1>
</div>

<!-- 🔍 SEARCH -->
<form method="GET" class="search-box">
    <input type="text" name="search" placeholder="Search by name..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<?php
$total=0;$avg=0;$top=0;$rows=[];

if($result && $result->num_rows>0){
    while($r=$result->fetch_assoc()){
        $rows[]=$r;
        $total++;
        $avg += (int)$r['score'];
        if($r['score']>$top) $top=$r['score'];
    }
    $avg = $total>0 ? round($avg/$total) : 0;
}
?>

<?php if(count($rows)>0): ?>

<p>Total: <?= $total ?> | Avg: <?= $avg ?> | Top: <?= $top ?></p>

<table>
<tr>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Specialty</th>
<th>Education</th>
<th>Company</th>
<th>Score</th>
<th>Readiness</th>
<th>Skill %</th>
<th>Projects</th>
<th>Experience</th>
<th>Salary</th>
<th>Date</th>
</tr>

<?php foreach($rows as $row):
$sc=(int)$row['score'];
$class=$sc>80?'good':($sc>=60?'warn':'bad');
?>

<tr>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= htmlspecialchars($row['role']) ?></td>
<td><?= htmlspecialchars($row['specialty']) ?></td>
<td><?= htmlspecialchars($row['education']) ?></td>
<td><?= htmlspecialchars($row['company']) ?></td>
<td class="score-cell"><?= $row['score'] ?></td>
<td class="<?= $class ?>"><?= $row['readiness_label'] ?></td>
<td><?= $row['skill_match_percent'] ?>%</td>
<td><?= $row['projects'] ?></td>
<td><?= $row['experience_years'] ?>y <?= $row['experience_months'] ?>m</td>
<td>₹<?= number_format($row['salary_expectation'],1) ?></td>
<td><?= $row['analysis_date'] ?></td>
</tr>

<?php endforeach; ?>
</table>

<?php else: ?>

<div class="empty-state">
<?php if(!empty($search)): ?>
<p>No results found for "<strong><?= htmlspecialchars($search) ?></strong>"</p>
<?php else: ?>
<p>No data available</p>
<?php endif; ?>
</div>

<?php endif; ?>

</div>

</body>
</html>
