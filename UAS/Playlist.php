<?php
include_once("conn.php");
session_start();

$playlist = $_GET['id_playlist'] ?? '';
$playlist = mysqli_real_escape_string($conn, $playlist);

// Query untuk mengambil data musik berdasarkan playlist
$MusikPlaylist = "SELECT musik.id_musik, musik.CoverLagu, musik.NamaLagu, artis.NamaArtis 
FROM playlist_musik
JOIN musik ON musik.id_musik = playlist_musik.id_musik
JOIN artis ON musik.id_artis = artis.id_artis 
WHERE playlist_musik.id_playlist = '$playlist'";

$musikResult = mysqli_query($conn, $MusikPlaylist);
if (!$musikResult) {
    echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
}

// Query untuk mendapatkan nama playlist berdasarkan ID
$playlistNameQuery = "SELECT NamaPlaylist FROM playlist WHERE id_playlist = '$playlist'";
$playlistNameResult = mysqli_query($conn, $playlistNameQuery);

if (!$playlistNameResult) {
    echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
} else {
    $playlistNameRow = mysqli_fetch_assoc($playlistNameResult);
    if ($playlistNameRow) {
        $playlistName = $playlistNameRow['NamaPlaylist'];
    } else {
        $playlistName = 'Unknown playlist';
    }
}

if (isset($_POST['Delete'])) {
    // Proses penghapusan
    $deleteQuery = "DELETE FROM playlist_musik WHERE id_playlist = '$playlist'";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    $deletePlaylistQuery = "DELETE FROM playlist WHERE id_playlist = '$playlist'";
    $deletePlaylistResult = mysqli_query($conn, $deletePlaylistQuery);

    if ($deleteResult) {
        echo "<script>alert('Playlist berhasil dihapus'); window.location.href='UserPages.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus playlist');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Montserrat+Alternates:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans+KR:wght@100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="PlaylistStyle.css" />
    <title>Playlist: <?php echo htmlspecialchars($playlistName); ?></title>
</head>
<body>
<nav>
    <ul>
        <li><a href="Main.php">Home</a></li>
        <li><a href="Plan.html">Plan</a></li>
        <li><a href="UserPages.php">User</a></li>
        <li><a id="logout">Logout</a></li>
    </ul>
</nav>
<div class="container">
    <div class="playlistContainer">
        <div class="HeaderContainer">
            <h2>Playlist: <?php echo htmlspecialchars($playlistName); ?></h2>
            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus playlist <?php echo htmlspecialchars($playlistName); ?>?')">
                <button  class="deletebtn" type="submit" name="Delete">Hapus Playlist</button>
            </form>
        </div>

        <?php if (mysqli_num_rows($musikResult) === 0): ?>
            <h2 style="display: flex; width: 100%; height:100%; color: white; justify-content: center;">No Song Found In This Playlist</h2>
        <?php else: ?>
            <div class="playlistList">
                <?php while ($row = mysqli_fetch_assoc($musikResult)) { ?>
                    <a class="playlistItem">
                        <img src="<?php echo htmlspecialchars($row['CoverLagu']); ?>" alt="Song Cover" />
                        <p onclick="window.location.href='Main.php?id_musik=<?= $row['id_musik'] ?>'"><?php echo htmlspecialchars($row['NamaLagu']); ?> </p>
                        <p onclick="window.location.href='ArtistPage.php?NamaArtis=<?= $row['NamaArtis'] ?>'"><?php echo htmlspecialchars($row['NamaArtis']); ?></p>
                    </a>
                <?php } ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    document.getElementById('logout').addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin keluar?')) {
            alert('Anda telah keluar dari akun Anda.');
            window.location.href = 'Logout.php';
        }
    });
</script>
</body>
</html>
