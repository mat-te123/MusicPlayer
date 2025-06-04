<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="RegisterPage_style.css">
    <title>Register</title>
</head>
<body>
    <h1> Ayoo U Need Register<i>!!!</i></h1>
    <?php
    include_once ("Conn.php");
    if(isset($_POST['btnRegister'])) {
        $email = $_POST['Email'];
        $username = $_POST['UserName'];
        $password = $_POST['Password'];
        $Status = 'ST01';
        $PhotoProfile = 'uploads/DefaultProfile.svg'; // Default profile picture

        // Check if the email or username already exists
        $query = "SELECT * FROM user WHERE Email='$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Email already exists.')</script>";
        } else {
            // Ambil id_user terakhir
            $query_id = "SELECT id_user FROM user ORDER BY id_user DESC LIMIT 1";
            $result_id = mysqli_query($conn, $query_id);
            $last_id = "US001"; // default jika kosong

            if ($row = mysqli_fetch_assoc($result_id)) {
                $last_id = $row['id_user'];
            }

            // Ambil angka dan tambahkan
            $num = intval(substr($last_id, 2)) + 1;
            $new_id = "US" . str_pad($num, 3, "0", STR_PAD_LEFT);  

            // Insert the new user into the database
            $insertQuery = "INSERT INTO user (id_user,Email, UserName, id_status, Password) VALUES ('$new_id','$email', '$username', '$Status','$password')";
            if (mysqli_query($conn, $insertQuery)) {
                echo "<script>alert('Registration successful!');</script>";
                echo "<script>window.location.href='NewComePage.php';</script>";
            } else {
                echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
            }
        }
    }
    ?>
    <div class="container">
        <form action="Register.php" method="POST">
            <div class="form-group">
                <input type="text" name="Email" id="Email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="text" name="UserName" id="UserName" placeholder="UserName" required>
            </div>
            <div class="form-group">
                <input type="password" name="Password" id="Password" placeholder="Password" required>
            </div>
            <div class="btn">
                <button type="submit" name="btnRegister" id="btnRegister">Register</button>
            </div>
            <p>Already have an account? <a href="Index.php">Login here</a></p>
        </form>
    </div>  
</body>
</html>