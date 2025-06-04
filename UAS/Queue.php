<?php
session_start();
include_once("conn.php");

// Mengambil data 
$username = $_SESSION['UserName'];

// ambil info
$query = "SELECT UserName, NamaStatus, id_user, PhotoProfile 
          FROM user 
          JOIN status_user ON user.id_status = status_user.id_status 
          WHERE UserName='$username'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$id_user = $data['id_user'];

// PageInation
$limit = 5;
$pages = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($pages - 1) * $limit;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM user");
$totalRow = mysqli_fetch_assoc($totalQuery);
$total = $totalRow['total'];
$pages = ceil($total / $limit);

$result = mysqli_query($conn, "SELECT * FROM user ORDER BY id_user ASC LIMIT $start, $limit");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="QueueStyle.css">
    <title>UserPage</title>
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
        <div class="QueueSong">
            <h2>
                Queue Song
            </h2>
            <div class="QueueSongItem">
                <?php
                $QueueQuery = "SELECT musik.NamaLagu, artis.NamaArtis, musik.CoverLagu
                from queue
                join musik on queue.id_musik = musik.id_musik
                join artis on musik.id_artis = artis.id_artis 
                WHERE id_user = '$id_user' 
                order by queue.urutan asc 
                limit $start, $limit";
                $result = mysqli_query($conn, $QueueQuery);
                if (!$result) {
                    echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
                }
                ?>
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <h2 style="display: flex; font-size:larger; width: 100%; height:100%; color: white; justify-content: center; align-items:center;">Antrian Kosong</h2>
                <?php else: ?>
                    <?php while ($queue = mysqli_fetch_assoc($result)): ?>
                        <img src="<?php echo $queue['CoverLagu']; ?>" alt="Song Cover">
                        <p href="SongName" class="SongName">
                            <?php echo $queue['NamaLagu']; ?>
                        </p>
                        <a href="SongName" class="ArtistName">
                            <?php echo $queue['NamaArtis']; ?>
                        </a>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="PageInation">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    
</body>
</html>