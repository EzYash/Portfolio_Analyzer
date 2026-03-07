<?php
require "auth.php";           // developer auth – you may later split this
include "db.php";

$sql = "
    SELECT 
        d.name, d.email, d.gender,
        a.role, a.company, a.company_role,
        a.score, a.readiness_label, a.skill_match_percent,
        a.projects, a.experience_years, a.experience_months,
        a.analysis_date, a.salary_expectation
    FROM analyses a
    JOIN developers d ON a.developer_id = d.id
    ORDER BY a.score DESC, a.analysis_date DESC
";

$result = $conn->query($sql);

// For safety – in real app you would paginate
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Portfolio Analyzer – Admin/Recruiter Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background:#0f172a; color:#e2e8f0; padding:30px; }
        h1 { color:#c7d2fe; }
        table { width:100%; border-collapse:collapse; background:#1e293b; margin-top:20px; }
        th, td { padding:12px; border:1px solid #334155; text-align:left; }
        th { background:#0f172a; color:#c7d2fe; }
        tr:hover { background:#334155; }
        .na { color:#94a3b8; }
    </style>
</head>
<body>

<h1>Portfolio Analysis Overview</h1>
<p style="color:#94a3b8;">Showing all developer analyses (most recent & highest scoring first)</p>

<?php if ($result && $result->num_rows > 0): ?>
<table>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Gender</th>
    <th>Role</th>
    <th>Company</th>
    <th>Score</th>
    <th>Readiness</th>
    <th>Skill Match %</th>
    <th>Projects</th>
    <th>Experience</th>
    <th>Salary (LPA)</th>
    <th>Date</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['name'] ?? '—') ?></td>
    <td><?= htmlspecialchars($row['email'] ?? '—') ?></td>
    <td class="na"><?= htmlspecialchars(ucfirst($row['gender'] ?? '—')) ?></td>
    <td><?= htmlspecialchars($row['role'] ?? '—') ?></td>
    <td><?= htmlspecialchars($row['company'] ?? '—') ?></td>
    <td><strong><?= $row['score'] ?? '—' ?></strong></td>
    <td><?= htmlspecialchars($row['readiness_label'] ?? '—') ?></td>
    <td><?= $row['skill_match_percent'] ?? '—' ?>%</td>
    <td><?= htmlspecialchars($row['projects'] ?? '—') ?></td>
    <td><?= $row['experience_years'] ?? 0 ?>y <?= $row['experience_months'] ?? 0 ?>m</td>
    <td><?= $row['salary_expectation'] ? "₹" . number_format($row['salary_expectation'], 1) : '—' ?></td>
    <td><?= htmlspecialchars($row['analysis_date'] ?? '—') ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p style="padding:30px; background:#1e293b; border-radius:8px;">No analyses found yet.</p>
<?php endif; ?>

</body>
</html>
