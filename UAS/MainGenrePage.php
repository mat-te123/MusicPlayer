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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Montserrat+Alternates:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans+KR:wght@100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="MainGenrePage.css">
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
        <div class="GenreContainer">
            <div class="header">
                <h1>Find Your Vibe</h1>
                <a href="Main.php#Genre">Back</a>
            </div>
            <div class="GenreItemContainer">
                <?php while ($row = mysqli_fetch_assoc($GenreResult)): ?>
                    <a class="GenreItem" onclick="window.location.href='GenrePage.php?genre_id=<?= $row['id_genre'] ?>'">
                        <img src="<?= $row['Image'] ?? 'Asset/ProfileDummy.png' ?>" alt="Genre Icon">
                    </a>
                <?php endwhile; ?>
            </div>
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