<?php
    include_once("conn.php");
    session_start();

    $GenreQuery = "SELECT * FROM genre";
    $GenreResult = mysqli_query($conn, $GenreQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="MainGenrePage.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="Main.php">Home</a></li>
            <li><a href="queue.php">Queue</a></li>
            <li><a href="Plan.html">Plan</a></li>
            <li><a href="UserPages.php">User</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="GenreContainer">
            <h1>Find Your Vibe</h1>
            <div class="GenreItemContainer">
                <?php while ($row = mysqli_fetch_assoc($GenreResult)): ?>
                    <a class="GenreItem" onclick="window.location.href='GenrePage.php?genre_id=<?= $row['id_genre'] ?>'">
                        <img src="<?= $row['Image'] ?? 'Asset/ProfileDummy.png' ?>" alt="Genre Icon">
                        <p><?= htmlspecialchars($row['NamaGenre']) ?></p>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

    </div>
    
</body>
</html>