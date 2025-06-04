<?php
// Database connection
include_once("conn.php");
session_start();
// Check if user is logged in
if (!isset($_SESSION['id_user'])) {
    echo "You must be logged in to access this page.";
    exit;
}
// Get user ID from session
$user_id = $_SESSION['id_user'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $role = $_POST['role'] ?? '';

    if ($role === 'Premium') {
        $StatusQuery = "SELECT id_status FROM users WHERE id_user = '$user_id'";
        $StatusResult = mysqli_query($conn, $StatusQuery);

        if ($row && isset($_SESSION['id_status']) && $_SESSION['id_status'] == $row['id_status']) {
            echo "<script>alert('You are already a premium user.'); window.location.href='UserPages.php';</script>";
            exit;
        }else {
            $query = "UPDATE user SET role = 'Premium' WHERE id_user = '$user_id'";
            $result = mysqli_query($conn, $query);
            if (!$result) {
                echo "Error updating role: " . mysqli_error($conn);
                exit;
            }
            echo "<script>alert('Your role has been upgraded to premium.');</script>";
            echo "<script>window.location.href='UserPages.php';</script>";
        }
    } else {
        // Do nothing for standard
        echo "<script>alert('You are already a standard user.');</script>";
        ECHO "<script>window.location.href='UserPages.php';</script>";
    }
} else {
    echo "Invalid request or not logged in.";
}

$conn->close();
?>