<?php
// Include file koneksi database
include_once("Connection.php");

// Cek apakah ada ID yang dikirimkan melalui URL
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Sanitasi ID agar aman sebagai integer

    // Query untuk menghapus data user berdasarkan id_user
    $result = mysqli_query($conn, "DELETE FROM user WHERE id_user = '$id'");

    // Redirect ke halaman utama setelah penghapusan
    header("Location: index.php");
} else {
    // Jika tidak ada ID dikirimkan, kembali ke halaman utama
    header("Location: index.php");
}
?>
