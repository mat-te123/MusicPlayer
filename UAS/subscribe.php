<?php
// Database connection
include_once("conn.php");
include_once("UserData.php");

// Check if user is logged in
session_start();
if (!isset($_SESSION['id_user'])) {
    echo "You must be logged in to access this page.";
    exit;
}

// Get user ID from session
$user_id = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['Status'] ?? '';

    // Ambil status user terkini dari database
    $statusQuery = "SELECT id_status FROM user WHERE id_user = '$user_id'";
    $statusResult = mysqli_query($conn, $statusQuery);
    $statusRow = mysqli_fetch_assoc($statusResult);
    $userStatus = $statusRow['id_status']; // ST01 atau ST02

    // Jika ingin upgrade ke Premium
    if ($role === 'Premium') {
        if ($userStatus === 'ST02') {
            echo "<script>alert('You are already a premium user.'); window.location.href='UserPages.php?id_user=$user_id';</script>";
            exit;   
        }

        // Cek jumlah favorit
        $favoriteQuery = "SELECT COUNT(*) as total FROM favorit WHERE id_user = '$user_id'";
        $favoriteResult = mysqli_query($conn, $favoriteQuery);
        $favoriteRow = mysqli_fetch_assoc($favoriteResult);

        if ($favoriteRow['total'] < 5) {
            echo "<script>alert('You need at least 5 favorites to upgrade to premium.');</script>";
            echo "<script>window.location.href='UserPages.php?id_user=$user_id';</script>";
            exit;
        }

        // Upgrade ke Premium (status ST02)
        $updateQuery = "UPDATE user SET id_status = 'ST02' WHERE id_user = '$user_id'";
        $updateResult = mysqli_query($conn, $updateQuery);
        $_SESSION['id_status'] = 'ST02';

        if (!$updateResult) {
            echo "Error updating status: " . mysqli_error($conn);
            exit;
        }

        echo "<script>alert('Your role has been upgraded to premium.');</script>";
        echo "<script>window.location.href='UserPages.php?id_user=$user_id';</script>";

    // Jika ingin downgrade ke Standard
    } else if ($role === 'Standard') {
        if ($userStatus === 'ST01') {
            echo "<script>alert('You are already a standard user.');</script>";
            echo "<script>window.location.href='UserPages.php?id_user=$user_id';</script>";
            exit;
        }

        // Downgrade ke Standard (status ST01)
        $updateQuery = "UPDATE user SET id_status = 'ST01' WHERE id_user = '$user_id'";
        $updateResult = mysqli_query($conn, $updateQuery);
        $_SESSION['id_status'] = 'ST01';

        if (!$updateResult) {
            echo "Error updating status: " . mysqli_error($conn);
            exit;
        }

        echo "<script>alert('Your role has been downgraded to standard.');</script>";
        echo "<script>window.location.href='UserPages.php?id_user=$user_id';</script>";
    } else {
        // Role tidak valid
        echo "<script>alert('Invalid role selection.');</script>";
        echo "<script>window.location.href='UserPages.php?id_user=$user_id';</script>";
        exit;
    }
}

?>
