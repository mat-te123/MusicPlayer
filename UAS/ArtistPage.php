<?php
include_once("conn.php");
session_start();

$namaArtis = $_GET['NamaArtis'] ?? '';
$namaArtis = mysqli_real_escape_string($conn, $namaArtis);

// Ambil info artis
$queryArtis = "SELECT * FROM artis WHERE NamaArtis = '$namaArtis'";
$resultArtis = mysqli_query($conn, $queryArtis);
$artis = mysqli_fetch_assoc($resultArtis);

// Ambil lagu dari artis
$queryLagu = "SELECT * FROM musik WHERE id_artis = '{$artis['id_artis']}'";
$resultLagu = mysqli_query($conn, $queryLagu);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Montserrat+Alternates:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans+KR:wght@100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="ArtistPageStyle.css">
    <title>Artist: <?php echo htmlspecialchars($namaArtis); ?></title>
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
        <div class="UserName">
            <div class="PhotoProfile">
                <img src="<?php echo $artis['ProfileArtis']; ?>" alt="Profile Picture">
            </div>
            <div class="InfoProfile">
                <div class="InfoProfile_Name">
                    <h2>Artist Name</h2>
                    <p><?php echo htmlspecialchars($artis['NamaArtis'] ?? 'Unknown'); ?></p>
                </div>
            </div>
        </div>

        <div class="FavoriteSong">
            <h2>Artist Songs</h2>
            <?php if (mysqli_num_rows($resultLagu) > 0): ?>
                <?php while ($lagu = mysqli_fetch_assoc($resultLagu)): ?>
                    <div class="FavoriteSongItem">
                        <img src="<?php echo htmlspecialchars($lagu['CoverLagu']); ?>" alt="Song Cover">
                        <a href="Main.php?id_musik=<?= $lagu['id_musik'] ?>" class="SongName"><?php echo htmlspecialchars($lagu['NamaLagu']); ?></a>
                        <a href="#" class="ArtistName"><?php echo htmlspecialchars($namaArtis); ?></a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Tidak ada lagu ditemukan.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
