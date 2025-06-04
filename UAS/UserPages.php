<?php
session_start();
include_once("conn.php");

// Ambil data user dari session
$username = $_SESSION['UserName'];
$plan = $_SESSION['id_status'];

// Ambil info user dari database
$query = "SELECT UserName, NamaStatus, id_user, PhotoProfile 
          FROM user 
          JOIN status_user ON user.id_status = status_user.id_status 
          WHERE UserName='$username' AND user.id_status='$plan'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$id_user = $data['id_user'];


if (isset($_POST['changeProfilePictureBtn'])) {
    // Cek apakah file diupload
    if (isset($_FILES['PhotoProfile']) && $_FILES['PhotoProfile']['error'] == 0) {
        $fileTmpPath = $_FILES['PhotoProfile']['tmp_name'];
        $fileName = $_FILES['PhotoProfile']['name'];

        
        // Tentukan direktori penyimpanan
        $uploadFileDir = 'uploads/';
        $newFileName = uniqid() . '.' . basename($fileName);
        $dest_path = $uploadFileDir . $fileName;
        // Pindahkan file ke direktori tujuan
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Update foto profil di database
            $updateQuery = "UPDATE user SET PhotoProfile='$dest_path' WHERE id_user='$id_user'";
            if (mysqli_query($conn, $updateQuery)) {
                $query = "SELECT PhotoProfile FROM user WHERE id_user='$id_user'";
                $result = mysqli_query($conn, $query);
                $newpic = mysqli_fetch_assoc($result);
                $data['PhotoProfile'] = $newpic['PhotoProfile'];
                echo "<script>alert('Foto profil berhasil diubah!');</script>";
            } else {
                echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
            }
        } else {
            echo "<script>alert('Gagal mengunggah file.');</script>";
        }
    }
}
    // Jika tidak ada file yang diupload, tetap tampilkan foto profil lama
$data['PhotoProfile'] = !empty($data['PhotoProfile']) ? $data['PhotoProfile'] : 'uploads/DefaultProfile.svg';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="UserPageStyle.css">
    <title>UserPage</title>
</head>
<body>

<nav>
    <ul>
        <li><a href="Main.php">Home</a></li>
        <li><a href="Queue.php">Queue</a></li>
        <li><a href="Plan.html">Plan</a></li>
        <li><a href="UserPages.php">User</a></li>
    </ul>
</nav>

<script>
    function addPlaylist() {
        document.getElementById('popupForm').style.display = 'flex';
        document.getElementById('blur-background').style.display = 'block';
    }

    function closePopup() {
        document.getElementById('popupForm').style.display = 'none';
        document.getElementById('changeProfilePicturePopup').style.display = 'none';
        document.getElementById('blur-background').style.display = 'none';
    }

    function changeProfilePicture() {
        document.getElementById('changeProfilePicturePopup').style.display = 'flex';
        document.getElementById('blur-background').style.display = 'block';
    }
</script>

<div class="container">
    <!-- <div class="edit">
        <button class="btn" onclick="changeProfilePicture()">edit</button>
    </div>     -->
    <div class="UserName">
        <div class="PhotoProfile">
            <img src="Asset/Edit.svg" alt="Edit Icon" class="EditIcon" onclick="changeProfilePicture()">
            <img src="<?php echo $data['PhotoProfile']; ?>" alt="Profile Picture" class="Picture">
        </div>
        <div class="InfoProfile">
            <div class="InfoProfile_Name">
                <h2 id="UserName">UserName</h2>
                <p><?php echo $data['UserName']; ?></p>
            </div>
            <div class="InfoProfile_Name">
                <h2>Plan</h2>
                <p><?php echo $data['NamaStatus']; ?></p>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="Playlist">
        <div class="PlaylistHeader">
            <h2>Playlist</h2>
            <img src="Asset/Playlisy.svg" alt="Add Playlist Icon" id="AddPlaylistIcon" onclick="addPlaylist()">
        </div>
        <div class="PlaylistItemContainer">
            <?php
            // Ambil semua playlist dari database
            $playlistQuery = "SELECT * FROM playlist WHERE id_user = '$id_user' order by id_playlist ASC LIMIT 5";
            $playlistResult = mysqli_query($conn, $playlistQuery);
            if (!$playlistResult) {
                echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
            }
            ?>
            <!-- Cek apakah ada playlist -->
            <?php if (mysqli_num_rows($playlistResult) === 0): ?>
                <h2 style="display: flex; width: 100%; height:100%; color: white; justify-content: center;">Belum ada Playlist</h2>
            <?php else: ?>
                <?php  while ($playlist = mysqli_fetch_assoc($playlistResult)): ?>
                    <div class="PlaylistItem">
                        <img src="<?php echo $playlist['image']; ?>" alt="Playlist Icon">
                        <p><?php echo htmlspecialchars($playlist['NamaPlaylist']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="FavoriteSong">
        <h2>Admin Favorite Song</h2>
        <?php
        $favoriteSongQuery = "SELECT musik.NamaLagu, artis.NamaArtis, musik.CoverLagu 
        FROM favorit 
        join musik on favorit.id_musik = musik.id_musik
        join artis on musik.id_artis = artis.id_artis 
        WHERE id_user = '$id_user' 
        order by favorit.id_musik asc 
        limit 5";
        $favoriteSongResult = mysqli_query($conn, $favoriteSongQuery);
        if (!$favoriteSongResult) {
            echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
        }
        ?>
        <?php if (mysqli_num_rows($favoriteSongResult) === 0): ?>
            <h2 style="display: flex; width: 100%; height:100%; color: white; justify-content: center;">Belum ada Lagu Favorit</h2>
        <?php else: ?>
            <?php  while ($favoriteSong = mysqli_fetch_assoc($favoriteSongResult)): ?>
                <div class="FavoriteSongItem">
                    <img src="<?php echo $favoriteSong['CoverLagu']; ?>" alt="Song Cover">
                    <p class="SongName" id="SongName"><?php echo htmlspecialchars($favoriteSong['NamaLagu']); ?></p>
                    <a href="<?php echo $favoriteSong['NamaArtis']; ?>" class="ArtistName" id="ArtistName"><?php echo htmlspecialchars($favoriteSong['NamaArtis']); ?></a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>
<div id="blur-background"></div>
<!-- Popup form -->

<div id="popupForm" class="popup-overlay">
    <form action="TambahPlaylist.php" method="post" class="FormContainer">
        <div class="popup-content">
            <h2>Tambah Playlist Baru</h2>
            <input type="text" name="playlist_name" placeholder="Nama Playlist" required>
        </div>
        <div class="form-buttons">
            <button type="submit" name="addPlaylistBtn">Tambah</button>
            <button type="button" onclick="closePopup()">Batal</button>
        </div>
    </form>
</div>

<!-- Popup form for changing profile -->
<div id="changeProfilePicturePopup" class="popup-overlay" >
    <form method="POST" class="FormContainer" enctype="multipart/form-data">
        <div class="popup-content">
            <h2>Ganti Foto Profil</h2>
            <input type="file" name="PhotoProfile" accept="image/*" required>
        </div>
        <div class="form-buttons">
            <button type="submit" name="changeProfilePictureBtn">Ganti</button>
            <button type="button" onclick="closePopup()">Batal</button>
        </div>
    </form>
</div>

</body>
</html>

