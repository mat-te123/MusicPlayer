<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="LoginPage_style.css">
    <title>Login</title>
</head>
<body>
    <h1> Hello Awesome <i>!!!</i></h1>
    <?php
    include_once ("conn.php");

    session_start();
    
    if(isset($_POST['btnLogin'])) {
        $identifier = $_POST['identifier'];
        $password = $_POST['Password'];
        

        // Check if the user exists
        $query = "SELECT * FROM user WHERE (Email='$identifier' OR UserName='$identifier') AND Password='$password'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {

            $user = mysqli_fetch_assoc($result);

            $_SESSION['UserName'] = $user['UserName'];
            $_SESSION['id_status'] = $user['id_status'];
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['Email'] = $user['Email'];
            $_SESSION['PhotoProfile'] = $user['PhotoProfile'];

            echo "<script>alert('Login successful!');</script>";
            echo "<script>window.location.href='UserPages.php';</script>";
        } else {
            echo "<script>alert('Invalid email/username or password.')</script>";
        }
    }
    ?>

    <div class="container">
        <form action="Index.php" method="POST">
            <div class="form-group">
                <input type="text" name="identifier" id="identifier" placeholder="Email or UserName" required>
            </div>
            <div class="form-group">
                <input type="password" name="Password" id="Password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <button type="submit" name="btnLogin" id="btnLogin">Login</button>
            </div>
            <p>Not have an account? <a href="Register.php">Register here</a></p>
        </form>
    </div>    
</body>
</html>