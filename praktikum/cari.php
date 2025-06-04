<?php
// Include file koneksi database
include_once("Connection.php");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cari Data User</title>
    <link rel="stylesheet" href="style.css"> <!-- jika kamu pakai file CSS terpisah -->
    <style>
        /* Tambahkan styling jika tidak pakai CSS terpisah */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        .btn {
            padding: 8px 12px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Cari Data User</h2>

    <!-- Form pencarian -->
    <form method="GET" action="cari.php">
        <input type="text" name="keyword" placeholder="Cari UserName atau id..." value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" required>
        <button type="submit" class="btn">Cari</button>
        <a href="indextest.php" class="btn">Kembali</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>id_user</th>
                <th>UserName</th>
                <th>Status</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (isset($_GET['keyword'])) {
            $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
            $query = "SELECT * FROM user WHERE UserName LIKE '%$keyword%' OR id_user LIKE '%$keyword%' ORDER BY id_user ASC";
            $result = mysqli_query($conn, $query);
            $no = 1;

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>".$no++."</td>";
                    echo "<td>".$row['id_user']."</td>";
                    echo "<td>".$row['UserName']."</td>";
                    echo "<td>".$row['id_status']."</td>";
                    echo "<td>".$row['Email']."</td>";
                    echo "<td>";
                    echo "<a href='edit.php?id=".$row['id_user']."' class='btn btn-edit'>Edit</a> ";
                    echo "<a href='hapus.php?id=".$row['id_user']."' class='btn btn-delete' onclick='return confirm(\"Yakin ingin menghapus data?\")'>Hapus</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center'>Tidak ada data ditemukan</td></tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center'>Masukkan kata kunci pencarian</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
