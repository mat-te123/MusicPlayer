<?php
// Pastikan session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan koneksi sudah disiapkan
include_once('conn.php');

if (isset($_SESSION['UserName']) && isset($_SESSION['id_user'])) {
    $username = $_SESSION['UserName'];
    $iduser = $_SESSION['id_user'];

    $MainQuery = "SELECT * FROM USER WHERE UserName = '$username' AND ID_USER = '$iduser'";
    $result = mysqli_query($conn, $MainQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Sekarang kamu bisa akses data user di variabel $row
    } else {
        // Misal user tidak ditemukan
        $row = null;
    }
} else {
    $row = null;
}
?>
