<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>

        <?php
        include_once("Connection.php");

        if (!isset($_GET['id'])) {
            header("Location: index.php");
            exit();
        }

        $id = $_GET['id'];

        if (isset($_POST['update'])) {
            $username = $_POST['UserName'];
            $id_status = $_POST['id_status'];
            $email = $_POST['Email'];

            $errors = [];

            if (empty($username)) $errors[] = "Username tidak boleh kosong.";
            if (empty($id_status)) $errors[] = "Status tidak boleh kosong.";
            if (empty($email)) {
                $errors[] = "Email tidak boleh kosong.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format email tidak valid.";
            }

            $foto_baru = $_FILES['Foto']['name'];
            if ($foto_baru) {
                $foto_tmp = $_FILES['Foto']['tmp_name'];
                $foto_nama = uniqid() . '_' . $foto_baru;
                $folder = 'uploads/' . $foto_nama;
                move_uploaded_file($foto_tmp, $folder);

                // Hapus foto lama
                $resultFoto = mysqli_query($conn, "SELECT PhotoProfile FROM user WHERE id_user='$id'");
                $rowFoto = mysqli_fetch_assoc($resultFoto);
                if (!empty($rowFoto['PhotoProfile']) && file_exists('uploads/' . $rowFoto['PhotoProfile'])) {
                    unlink('uploads/' . $rowFoto['PhotoProfile']);
                }

                $foto_sql = ", PhotoProfile='$foto_nama'";
            } else {
                $foto_sql = "";
            }

            if (empty($errors)) {
                $result = mysqli_query($conn, "UPDATE user SET 
                    UserName = '$username', 
                    id_status = '$id_status', 
                    Email = '$email' 
                    $foto_sql
                    WHERE id_user = '$id'");

                if ($result) {
                    echo "<div style='padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;'>
                            Data berhasil diperbarui. <a href='indextest.php'>Lihat Data</a>
                          </div>";
                } else {
                    echo "<div style='padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 15px;'>
                            Error: " . mysqli_error($conn) . "
                          </div>";
                }
            } else {
                echo "<div style='padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 15px;'>
                        <ul>";
                foreach ($errors as $error) {
                    echo "<li>$error</li>";
                }
                echo "</ul></div>";
            }
        }

        $result = mysqli_query($conn, "SELECT * FROM user WHERE id_user = '$id'");
        if (mysqli_num_rows($result) == 0) {
            header("Location: index.php");
            exit();
        }

        $row = mysqli_fetch_assoc($result);
        ?>

        <form action="edit.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">UserName</label>
                <input type="text" name="UserName" id="UserName" value="<?php echo $row['UserName']; ?>" required>
            </div>

            <div class="form-group">
                <label for="id_status">ID Status</label>
                <input type="text" name="id_status" id="id_status" value="<?php echo $row['id_status']; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="Email" id="Email" value="<?php echo $row['Email']; ?>" required>
            </div>

            <div class="form-group">
                <label for="foto">Foto</label>
                <?php if (!empty($row['PhotoProfile'])): ?>
                    <img src="uploads/<?php echo $row['PhotoProfile']; ?>" width="100" alt="Foto Profil">
                <?php endif; ?>
                <input type="file" name="Foto" id="Foto">
            </div>

            <div style="margin-top: 20px;">
                <input type="submit" name="update" value="Update" class="btn">
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
