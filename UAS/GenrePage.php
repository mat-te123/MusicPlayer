<?php
include_once("conn.php");
session_start();

$genre = $_GET['genre_id'] ?? '';

$genre = mysqli_real_escape_string($conn, $genre);
// Query untuk mengambil data musik berdasarkan genre

$MusikGenreQuery = "SELECT musik.id_musik, musik.CoverLagu, musik.NamaLagu, artis.NamaArtis FROM musik_genre
join musik on musik.id_musik = musik_genre.id_musik
join artis on musik.id_artis = artis.id_artis 
WHERE musik_genre.id_genre = '$genre'";

$MusikGenreResult = mysqli_query($conn, $MusikGenreQuery);
if (!$MusikGenreResult) {
    echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Montserrat+Alternates:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans+KR:wght@100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="GenrePageStyle.css">
    <title>Document</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="Main.php">Home</a></li>
            <li><a href="Plan.php">Playlist</a></li>
            <li><a href="UserPages.php">User</a></li>
            <li><a id="logout">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="GenreContainer">
            <?php
            // Query untuk mendapatkan nama genre berdasarkan ID
            $genreNameQuery = "SELECT NamaGenre FROM genre WHERE id_genre = '$genre'";
            $genreNameResult = mysqli_query($conn, $genreNameQuery);
            if (!$genreNameResult) {
                echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
            } else {
                $genreNameRow = mysqli_fetch_assoc($genreNameResult);
                if ($genreNameRow) {
                    $genreName = $genreNameRow['NamaGenre'];
                } else {
                    $genreName = 'Unknown Genre';
                }
            }
            ?>
            <h2>Genre: <?php echo htmlspecialchars($genreName); ?></h2>
            <?php if (mysqli_num_rows($MusikGenreResult) === 0): ?>
                <h2 style="display: flex; width: 100%; height:100%; color: white; justify-content: center;">No Song Found In This Genre</h2>
            <?php else: ?>
                <div class="GenreList">
                    <?php while ($row = mysqli_fetch_assoc($MusikGenreResult)) { ?>
                        <div class="GenreItem">
                            <img src="<?php echo htmlspecialchars($row['CoverLagu']); ?>" alt="Song Cover">
                            <p style="cursor: pointer;" class="NamaLagu" onclick="window.location.href='Main.php?id_musik=<?= $row['id_musik'] ?>'"><?php echo htmlspecialchars($row['NamaLagu']);?></p>
                            <p style="cursor: pointer;" class="NamaArtis" onclick="window.location.href='ArtistPage.php?NamaArtis=<?= $row['NamaArtis'] ?>'"><?php echo htmlspecialchars($row['NamaArtis']); ?></p>
                        </div>
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