<?php
include_once("conn.php");
session_start();
include_once("UserData.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="MainStyle.css">
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
        function OpenPopUp() {
            document.getElementById('blur-background').style.display = "block";
            document.getElementById('PopUpPlaylist').style.display = "flex";
        }

        function closePopUp() {
            console.log("MANTAP BANGGG")
            document.getElementById('blur-background').style.display = "none";
            document.getElementById('PopUpPlaylist').style.display = "none";
            document.getElementById('popupForm').style.display = "none";
        }

        function AddPlaylist() {
            document.getElementById('PopUpPlaylist').style.display = "none"
            document.getElementById('popupForm').style.display = "flex";
        }
    </script>
    <div class="container">
        <div class="SearchBar">
            <form action="search.php" method="get">
                <input type="text" name="query" placeholder="Search for songs, artists, or albums...">
            </form>
        </div>
        <div class="CurrentSong">
            <h2>Currently Playing</h2>
            <div class="SongContainer">
                <div class="PrevSongItem">
                    <img src="Asset/ProfileDummy.png" alt="prev Song Cover">
                    <div class="PrevSongInfo">
                        <p class="SongTitle">Song Title</p>
                    </div>
                </div>
                <div class="CurrentSongItem">
                    <img src="Asset/ProfileDummy.png" alt="prev Song Cover">
                    <div class="CurrentSongInfo">
                        <p class="SongTitle">Song Title</p>
                    </div>
                </div>
                <div class="NextSongItem">
                    <img src="Asset/ProfileDummy.png" alt="Current Song Cover">
                    <div class="NextSongInfo">
                        <p class="SongTitle">Song Title</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="PlayerControls">
        <div class="ControlButtons">
            <img src="Asset/prev.svg" alt="prev" class="ButtonPlayer">
            <img src="Asset/Play.svg" alt="play" class="ButtonPlayer">
            <img src="Asset/next.svg" alt="next" class="ButtonPlayer">
        </div>
        <div class="QuePlayContainer">
            <img src="Asset/Queue.svg" alt="Queue" class="ButtonPlayer" id="btn">
            <img src="Asset/Playlisy.svg" alt="Playlist" class="ButtonPlayer" id="btn" onclick="OpenPopUp()">
        </div>
    </div>
    <div class="container">
        <div class="GenreContainer">
            <div class="GenreHeader">
                <h1>Search Ur Genre</h1>
                <a href="MainGenrePage.php" class="ViewAllGenres">More</a>
            </div>
            <?php
            $GenreQuery = "SELECT * FROM genre ORDER BY id_genre ASC LIMIT 5";
            $GenreResult = mysqli_query($conn, $GenreQuery);
            ?> 
            <?php if (mysqli_num_rows($GenreResult) > 0): ?>
                <div class="itemContainer">
                    <?php while ($row = mysqli_fetch_assoc($GenreResult)): ?>
                        <a class="GenreItem" onclick="window.location.href='GenrePage.php?genre_id=<?= $row['id_genre'] ?>'">
                            <img src="<?= $row['Image'] ?? 'Asset/ProfileDummy.png' ?>" alt="Genre Icon">
                            <p><?= htmlspecialchars($row['NamaGenre']) ?></p>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No genres found.</p>
            <?php endif; ?>
        </div>
    </div>
    <div id="blur-background"></div>
    <!-- nambah lagu ke playlist -->
    <div class="popup-overlay" id="PopUpPlaylist">
        <form action="TambahPlaylist.php" method="post" class="FormContainer">
            <div class="popup-content">
                <h2>Pilih Playlist</h2>
                <!-- <input type="hidden" name="id_musik" value="<?php echo $_SESSION['CurrentSong']; ?>"> -->

                <?php
                $userId = $_SESSION['id_user'] ?? null;
                $PlaylistQuery = "SELECT * FROM PLAYLIST WHERE id_user = '$userId' LIMIT 4";
                $PlaylistResult = mysqli_query($conn, $PlaylistQuery);

                if (mysqli_num_rows($PlaylistResult) > 0): 
                    while ($playlist = mysqli_fetch_assoc($PlaylistResult)): ?>
                        <div class="item">
                            <img src="<?php echo $playlist['image'] ?? 'Asset/ProfileDummy.png'; ?>" alt="cover album">
                            <p><?php echo htmlspecialchars($playlist['NamaPlaylist']); ?></p>
                            <button type="submit" class="btn" name="insertPlaylistBtn" value="<?php echo $playlist['id_playlist']; ?>">
                                <img src="Asset/Playlisy.svg" alt="Add Playlist Icon" id="AddPlaylistIcon">
                            </button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: white;">Belum ada Playlist</p>
                <?php endif; ?>
            </div>

            <div class="form-buttons">
                <?php if (mysqli_num_rows($PlaylistResult) == 0): ?>
                    <button type="button" onclick="AddPlaylist()">Tambah</button>
                <?php endif; ?>
                <button type="button" onclick="closePopUp()">Cancel</button>
                <button type="button" onclick="AddPlaylist()">Tambah</button>
            </div>
        </form>
    </div>

    <!-- buat playlist -->
    <div id="popupForm" class="popup-overlay_1">
        <form action="TambahPlaylist.php" method="post" class="FormContainer_1">
            <div class="popup-content_1">
                <h2>Tambah Playlist Baru</h2>
                <input type="text" name="playlist_name" placeholder="Nama Playlist" required>
            </div>
            <div class="form-buttons_1">
                <button type="submit" name="addPlaylistBtn">Tambah</button>
                <button type="button" onclick="closePopUp()">Batal</button>
            </div>
        </form>
    </div>

</body>
</html>