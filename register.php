<?php
require "db.php";
session_start();

$error = "";
$success = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $name = $_POST["name"] ?? "";
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    if(!$name || !$email || !$password){
        $error = "All fields are required.";
    }else{

        // check if email already exists
        $stmt = $conn->prepare("SELECT id FROM developers WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $error = "Email already registered.";
        }else{

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO developers(name,email,password) VALUES(?,?,?)");
            $stmt->bind_param("sss",$name,$email,$hashedPassword);

            if($stmt->execute()){
                $success = "Account created successfully. You can now login.";
            }else{
                $error = "Registration failed.";
            }

        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div style="max-width:400px;margin:80px auto;font-family:Arial;">

<h2>Create Account</h2>

<?php if($error): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if($success): ?>
<p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST">

<div style="margin-bottom:15px;">
<label>Name</label><br>
<input type="text" name="name" required style="width:100%;padding:8px;">
</div>

<div style="margin-bottom:15px;">
<label>Email</label><br>
<input type="email" name="email" required style="width:100%;padding:8px;">
</div>

<div style="margin-bottom:15px;">
<label>Password</label><br>

<input type="password" id="passwordField" name="password" required style="width:100%;padding:8px;margin-bottom:8px;">

<button type="button"
        onclick="togglePassword()"
        style="width:100%;padding:8px;background:#f3f4f6;border:1px solid #ddd;border-radius:6px;cursor:pointer;">
Show Password
</button>

</div>

<button type="submit" style="padding:10px 16px;">Register</button>

</form>

<p style="margin-top:15px;">
Already have an account? <a href="login.php">Login</a>
</p>

</div>

<script>
function togglePassword(){
    const field = document.getElementById("passwordField");
    const btn = event.target;

    if(field.type === "password"){
        field.type = "text";
        btn.innerText = "Hide";
    }else{
        field.type = "password";
        btn.innerText = "Show";
    }
}
</script>

</body>
</html>