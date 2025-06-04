<?php
session_start();
include_once("Connection.php");

// if (!isset($_SESSION['login'])) {
//     header("Location: login.php");
//     exit;
// }

// Pagination setup
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM user");
$totalRow = mysqli_fetch_assoc($totalQuery);
$total = $totalRow['total'];
$pages = ceil($total / $limit);

$result = mysqli_query($conn, "SELECT * FROM user ORDER BY id_user ASC LIMIT $start, $limit");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 6px 12px;
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
            border-radius: 4px;
        }
        .btn-edit { background-color: #2196F3; }
        .btn-delete { background-color: #f44336; }
        .btn:hover { opacity: 0.8; }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Data User</h1>

        <a href="tambah.php" class="btn">Tambah User</a>
        <a href="cari.php" class="btn">Cari Data</a>
        <a href="logout.php?logout=true" class="btn" style="background-color: #555;">Logout</a>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID User</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Foto</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $no = $start + 1;
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>".$no++."</td>";
                        echo "<td>".$row['id_user']."</td>";
                        echo "<td>".$row['UserName']."</td>";
                        echo "<td>".$row['Email']."</td>";
                        echo "<td><img src='uploads/".$row['PhotoProfile']."' width='50'></td>";
                        echo "<td><a href='edit.php?id=".$row['id_user']."' class='btn btn-edit'>Edit</a> ";
                        echo "<a href='hapus.php?id=".$row['id_user']."' class='btn btn-delete' onclick='return confirm(\"Yakin ingin menghapus data?\")'>Hapus</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada data</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <a href="?page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
