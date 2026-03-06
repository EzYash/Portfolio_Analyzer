<?php
require "auth.php";
require "db.php";

// Get logged in user id
$userId = $_SESSION["user_id"] ?? null;

// Fetch previous analyses for this user
$stmt = $conn->prepare("SELECT role, score, company, analysis_date FROM analyses WHERE developer_id = ? ORDER BY analysis_date DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$hasRows = $result && $result->num_rows > 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Analysis History</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div style="max-width:900px;margin:40px auto;font-family:Arial, sans-serif;">

<h2>Your Portfolio Analysis History</h2>
<p style="color:#555;margin-bottom:20px;">
Here you can review all previous portfolio analyses you have performed.
</p>

<?php if($hasRows): ?>

<table style="width:100%;border-collapse:collapse;">
<tr style="background:#f3f4f6;">
<th style="padding:10px;border:1px solid #ddd;text-align:left;">Role</th>
<th style="padding:10px;border:1px solid #ddd;text-align:left;">Score</th>
<th style="padding:10px;border:1px solid #ddd;text-align:left;">Target Company</th>
<th style="padding:10px;border:1px solid #ddd;text-align:left;">Date</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td style="padding:10px;border:1px solid #ddd;"><?php echo htmlspecialchars($row["role"]); ?></td>
<td style="padding:10px;border:1px solid #ddd;"><?php echo htmlspecialchars($row["score"]); ?></td>
<td style="padding:10px;border:1px solid #ddd;"><?php echo htmlspecialchars($row["company"]); ?></td>
<td style="padding:10px;border:1px solid #ddd;"><?php echo htmlspecialchars($row["analysis_date"]); ?></td>
</tr>
<?php endwhile; ?>

</table>

<?php else: ?>

<div style="padding:30px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;">
<p style="margin:0;font-size:16px;">
You have not performed any portfolio analyses yet.
</p>
<p style="margin-top:10px;color:#666;">
Go to the analyzer and run your first analysis to see results here.
</p>
</div>

<?php endif; ?>

<div style="margin-top:25px;">
<a href="dashboard.php" style="text-decoration:none;color:#2563eb;font-weight:600;">
← Back to Dashboard
</a>
</div>

</div>

</body>
</html>
