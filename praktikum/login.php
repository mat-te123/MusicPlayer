<?php
session_start();

// Redirect jika sudah login
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['UserName']);
    $password = trim($_POST['password']);

    // Validasi input kosong
    if (empty($username) || empty($password)) {
        $error_message = "Username dan Password tidak boleh kosong!";
    } 
    // Cek akun default
    elseif ($username === 'admin' && $password === '123') {
        // Simpan sesi login
        $_SESSION['id_user'] = 0; // ID dummy karena tidak pakai DB
        $_SESSION['UserName'] = 'admin';

        session_regenerate_id(true); // Keamanan

        header("Location: indextest.php");
        exit();
    } 
    else {
        $error_message = "Username atau Password salah!";
    }
}
?>
