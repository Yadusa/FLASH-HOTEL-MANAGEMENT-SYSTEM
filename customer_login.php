<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password_hash FROM customer WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $dbPassword);
        $stmt->fetch();

        // Plain-text password comparison
           if (password_verify($password, $dbPassword)) {
            $_SESSION['customer_id'] = $id;
            $_SESSION['customer_username'] = $username;

            // Determine destination
            $redirect_url = "hotel.php"; // Default
            if (isset($_GET['redirect'])) {
                $target = $_GET['redirect'];
                $params = $_GET;
                unset($params['redirect']);
                $queryString = http_build_query($params);
                $redirect_url = $target . "?" . $queryString;
            }

            // Show success message and then redirect
            echo "<script>
                    alert('Login Successful! Welcome, " . htmlspecialchars($username) . ".');
                    window.location.href = '$redirect_url';
                  </script>";
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Login | The Obsidian</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { margin: 0; height: 100vh; background: linear-gradient(135deg, #111827, #1f2933); display: flex; justify-content: center; align-items: center; }
.login-card { background: #fff; width: 100%; max-width: 420px; padding: 45px 40px; border-radius: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.25); text-align: center; animation: fadeIn 0.8s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
.login-card h2 { margin-bottom: 6px; font-weight: 600; font-size: 26px; color: #111827; }
.login-card .subtitle { font-size: 14px; color: #6b7280; margin-bottom: 28px; }
.login-card input { width: 100%; padding: 13px 14px; margin-bottom: 16px; border-radius: 12px; border: 1px solid #d1d5db; font-size: 14px; transition: 0.25s; }
.login-card input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99,102,241,0.18); }
.login-card button { width: 100%; padding: 14px; border: none; border-radius: 14px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; font-size: 15px; font-weight: 500; cursor: pointer; transition: 0.3s ease; }
.login-card button:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(99,102,241,0.4); }
.error { background: #fee2e2; color: #b91c1c; padding: 11px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; }
.success { background: #dcfce7; color: #166534; padding: 11px; border-radius: 10px; font-size: 13px; margin-bottom: 18px; }
.login-card p { margin-top: 18px; font-size: 14px; color: #4b5563; }
.login-card a { color: #4f46e5; text-decoration: none; font-weight: 500; }
.login-card a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="login-card">
    <h2>Customer Login</h2>
    <p class="subtitle">Sign in to continue your booking</p>

    <?php 
    if (!empty($error)) echo "<div class='error'>$error</div>"; 
    if (isset($_GET['registered'])) echo "<div class='success'>Registration successful. Please log in.</div>"; 
    ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" novalidate>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
    </form>

    <p>No account yet? <a href="customer_register.php">Create one</a></p>
</div>

</body>
</html>
