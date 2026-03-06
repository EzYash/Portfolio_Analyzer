<?php
require "db.php";
session_start();

$error = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, name, password FROM developers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();

        if(password_verify($password, $user["password"])){
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];

            header("Location: index.php");
            exit();
        }else{
            $error = "Invalid password.";
        }
    }else{
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="assests/css/style.css">
</head>

<body>

<div style="max-width:400px;margin:120px auto;font-family:Arial;border:5px solid white;border-radius:10px;padding:30px;box-shadow:0 10px 25px rgba(0,0,0,0.08);background:inherit;text-align:center;">

<h2>Login</h2>

<?php if($error): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">

<div style="margin-bottom:15px;">
<label>Email</label><br>
<input type="email" name="email" required style="width:100%;padding:8px;">
</div>

<div style="margin-bottom:15px;">
<label>Password</label><br>
<input type="password" id="passwordField" name="password" required style="width:100%;padding:8px;">
<br>
<label style="display:inline-flex;align-items:center;margin-top:6px;gap:6px;cursor:pointer;">
<input type="checkbox" id="showPassBox"
onmousedown="showPassword()"
onmouseup="hidePassword()"
onmouseleave="hidePassword()">
Show Password
</label>
</div>

<button type="submit" style="padding:10px 16px;border-radius:8px;border:none;background:#4f46e5;color:white;cursor:pointer;transition:0.3s;box-shadow:0 0 0 rgba(79,70,229,0.6);" 
onmouseover="this.style.boxShadow='0 0 12px rgba(79,70,229,0.8)';" 
onmouseout="this.style.boxShadow='0 0 0 rgba(79,70,229,0.6)';">
Login
</button>

</form>

<p style="margin-top:15px;">
Don't have an account? <a href="register.php">Register</a>
</p>

</div>

</body>
<script>
function showPassword(){
    const field = document.getElementById("passwordField");
    field.type = "text";
}

function hidePassword(){
    const field = document.getElementById("passwordField");
    field.type = "password";
    document.getElementById("showPassBox").checked = false;
}
</script>
</html>
