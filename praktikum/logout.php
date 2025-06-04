<?php
// logout.php
session_start();

// Fungsi untuk logout yang aman
function secure_logout() {
    // 1. Hapus semua variabel session
    $_SESSION = array();
    
    // 2. Hapus session cookie jika ada
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // 3. Hancurkan session
    session_destroy();
    
    // 4. Regenerate session ID untuk keamanan
    session_start();
    session_regenerate_id(true);
}

// Proses logout
if (isset($_GET['logout']) || isset($_POST['logout'])) {
    // Log aktivitas logout (opsional)
    if (isset($_SESSION['UserName'])) {
        $username = $_SESSION['UserName'];
        $logout_time = date('Y-m-d H:i:s');
        
    }
    
    // Lakukan logout
    secure_logout();
    
    // Redirect ke halaman login dengan pesan
    header("Location: login.html?message=logged_out");
    exit();
}

// Jika tidak ada parameter logout, redirect ke dashboard
header("Location: indextest.php");
exit();
?>