<?php
require "db.php";
session_start();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $company   = trim($_POST["company"] ?? "");
    $name      = trim($_POST["name"] ?? "");
    $email     = trim($_POST["email"] ?? "");
    $password  = $_POST["password"] ?? "";

    if (!$company || !$name || !$email || !$password) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM recruiters WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO recruiters (company_name, recruiter_name, email, password) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $company, $name, $email, $hash);
            if ($stmt->execute()) {
                $success = "Recruiter account created. <a href='recruiter_login.php'>Login here</a>";
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Recruiter Register</title></head>
<body style="font-family:Arial; max-width:420px; margin:80px auto; padding:30px; background:#111827; color:white; border-radius:12px;">

<h2>Recruiter Registration</h2>

<?php if($error): ?><p style="color:#f87171;"><?=$error?></p><?php endif; ?>
<?php if($success): ?><p style="color:#34d399;"><?=$success?></p><?php endif; ?>

<form method="POST">
    <div style="margin:15px 0;">
        <label>Company Name</label><br>
        <input type="text" name="company" required style="width:100%; padding:10px; margin-top:6px;">
    </div>
    <div style="margin:15px 0;">
        <label>Your Name</label><br>
        <input type="text" name="name" required style="width:100%; padding:10px; margin-top:6px;">
    </div>
    <div style="margin:15px 0;">
        <label>Email</label><br>
        <input type="email" name="email" required style="width:100%; padding:10px; margin-top:6px;">
    </div>
    <div style="margin:15px 0;">
        <label>Password</label><br>
        <input type="password" name="password" required style="width:100%; padding:10px; margin-top:6px;">
    </div>
    <button type="submit" style="padding:12px 24px; background:#6366f1; color:white; border:none; border-radius:8px; cursor:pointer;">Create Recruiter Account</button>
</form>

<p style="margin-top:20px;">Already have an account? <a href="recruiter_login.php" style="color:#a5b4fc;">Login</a></p>
<p><a href="register.php" style="color:#94a3b8;">← I'm a Developer</a></p>

</body>
</html>