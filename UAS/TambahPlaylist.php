<?php
include_once('conn.php');
session_start();
include_once('UserData.php');

// Pastikan data user valid
$id_user = isset($row['id_user']) ? $row['id_user'] : null;
if ($id_user === null) {
    die("User tidak ditemukan. Harap login ulang.");
}

if (isset($_POST['addPlaylistBtn'])) {
    $playlist_name = $_POST['playlist_name'];
    $default_cover = "Asset/DefaultCover.svg";

    // Ambil ID terakhir
    $lastPlaylistQuery = "SELECT id_playlist FROM playlist ORDER BY id_playlist DESC LIMIT 1";
    $lastPlaylistResult = mysqli_query($conn, $lastPlaylistQuery);
    $lastId = "PL00";
    if ($lastPlaylistResult && mysqli_num_rows($lastPlaylistResult) > 0) {
        $lastRow = mysqli_fetch_assoc($lastPlaylistResult);
        $lastId = $lastRow['id_playlist'];
    }

    // Tambahkan angka ID
    $num = intval(substr($lastId, 2)) + 1;
    $newId = "PL" . str_pad($num, 2, "0", STR_PAD_LEFT);

    // Masukkan ke database
    $insert = "INSERT INTO playlist (id_playlist, NamaPlaylist, id_user, image) 
               VALUES ('$newId', '$playlist_name', '$id_user', '$default_cover')";
    $result = mysqli_query($conn, $insert);

    if (!$result) {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    } else {
        echo "<script>alert('Playlist berhasil ditambahkan!'); window.location.href='UserPages.php';</script>";
        exit;
    }
}

if (isset($_POST['insertPlaylistBtn'])) {
    $playlist_name = $_POST['playlist_name'];
    $Playlistid = $_POST['id_playlist'];

    $query = "INSERT INTO PLAYLIST_MUSIK (id_playlist,id_musik) VALUES ('$Playlistid','$id_musik')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    } else {
        echo "<script>alert('Lagu berhasil ditambahkan!'); window.location.href='Main.php';</script>";
        exit;
    }

}
?>
