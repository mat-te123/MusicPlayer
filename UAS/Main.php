<?php
include_once("conn.php");
session_start();
include_once("UserData.php");

$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$selectedSongId = isset($_GET['id_musik']) ? $_GET['id_musik'] : null;
$currentSong = null;
$nextSong = null;
$prevSong = null;
$searchResult = null;

// If a specific song is selected, get that song
if ($selectedSongId) {
    $query = "SELECT musik.*, artis.NamaArtis 
              FROM musik 
              JOIN artis ON musik.id_artis = artis.id_artis 
              WHERE musik.id_musik = '$selectedSongId'";
    $result = mysqli_query($conn, $query);
    $currentSong = mysqli_fetch_assoc($result);
    
    // Store current song in session
    $_SESSION['CurrentSong'] = $selectedSongId;
} elseif ($searchKeyword !== '') {
    // Query pencarian - get first result
    $query = "SELECT musik.*, artis.NamaArtis 
              FROM musik 
              JOIN artis ON musik.id_artis = artis.id_artis 
              WHERE musik.NamaLagu LIKE '%$searchKeyword%' OR artis.NamaArtis LIKE '%$searchKeyword%' 
              LIMIT 1";
    $result = mysqli_query($conn, $query);
    $currentSong = mysqli_fetch_assoc($result);
    
    if ($currentSong) {
        $_SESSION['CurrentSong'] = $currentSong['id_musik'];
    }

    // Query semua hasil (untuk tampilkan hasil pencarian)
    $searchQuery = "SELECT musik.*, artis.NamaArtis 
                    FROM musik 
                    JOIN artis ON musik.id_artis = artis.id_artis 
                    WHERE musik.NamaLagu LIKE '%$searchKeyword%' OR artis.NamaArtis LIKE '%$searchKeyword%'";
    $searchResult = mysqli_query($conn, $searchQuery);
}

// Ambil lagu berikut dan sebelumnya jika ada hasil
if ($currentSong) {
    $idMusik = $currentSong['id_musik'];

    // Next song (random selain current)
    $nextQuery = "SELECT musik.*, artis.NamaArtis 
                  FROM musik 
                  JOIN artis ON musik.id_artis = artis.id_artis 
                  WHERE musik.id_musik != '$idMusik' 
                  ORDER BY RAND() 
                  LIMIT 1";
    $nextResult = mysqli_query($conn, $nextQuery);
    $nextSong = mysqli_fetch_assoc($nextResult);

    // Previous song (ID lebih kecil)
    $prevQuery = "SELECT musik.*, artis.NamaArtis 
                  FROM musik 
                  JOIN artis ON musik.id_artis = artis.id_artis 
                  WHERE musik.id_musik < '$idMusik' 
                  ORDER BY musik.id_musik DESC 
                  LIMIT 1";
    $prevResult = mysqli_query($conn, $prevQuery);
    $prevSong = mysqli_fetch_assoc($prevResult);
}

// Optional: Store recently played songs
if ($currentSong && isset($_SESSION['id_user'])) {
    $userId = $_SESSION['id_user'];
    $songId = $currentSong['id_musik'];
    // You can add recently played functionality here if needed
}

if (isset($_POST['insertPlaylistBtn'])) {
    $Playlistid = $_POST['insertPlaylistBtn'];
    $id_musik = $_POST['currentSong'] ?? $currentSong['id_musik'];
    if (!$id_musik) {
        echo "<script>alert('No song selected to add to playlist.');</script>";
        echo "<script>window.location.href='Main.php';</script>";
        exit;
    }

    $songcheckquery = "SELECT * FROM playlist_musik WHERE id_playlist = '$Playlistid' AND id_musik = '$id_musik'";
    $songcheckresult = mysqli_query($conn, $songcheckquery);
    if (mysqli_num_rows($songcheckresult) > 0) {
        echo "<script>alert('Lagu sudah ada di playlist ini!');</script>";
        echo "<script>window.location.href='Main.php?id_musik=" . $id_musik . "';</script>";
        exit;
    }   

    $query = "INSERT INTO playlist_musik (id_playlist,id_musik) VALUES ('$Playlistid','$id_musik')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    } else {
        echo "<script>alert('Lagu berhasil ditambahkan!'); window.location.href='Main.php?id_musik=" . $id_musik . "';</script>";
        exit;
    }
}

if (isset($_POST['favoriteBtn'])) {
    $userId = $_POST['favoriteBtn'];
    $id_musik = $_POST['currentSong'];
    
    if ($id_musik === null || $id_musik === '') {
        echo "<script>alert('Lagi egga ada lagu bree.');</script>";
        echo "<script>window.location.href='Main.php';</script>";
        exit;
    } else {
        // Check if the song is already in favorites
        $checkQuery = "SELECT * FROM FAVORIT WHERE id_user = '$userId' AND id_musik = '$id_musik'";
        $checkResult = mysqli_query($conn, $checkQuery);
        $checkRow = mysqli_num_rows($checkResult);
        if ($checkRow > 0) {
            echo "<script>alert('Lagu sudah ada di favorit!');</script>";
            echo "<script>window.location.href='Main.php?id_musik=" . $id_musik . "';</script>";
            exit;
        } else {
            $query = "INSERT INTO FAVORIT (id_user, id_musik) VALUES ('$userId', '$id_musik')";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo "<script>alert('Tidak ada lagu yang diputar');</script>";
            } else {
                echo "<script>alert('Lagu berhasil ditambahkan ke favorit!'); window.location.href='Main.php?id_musik=" . $id_musik . "';</script>";
                exit;
            }
        }
    }

    
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
    <link rel="stylesheet" href="MainStyle.css">
    <title>Main Page</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="Main.php">Home</a></li>
            <li><a href="Plan.html">Plan</a></li>
            <li><a href="UserPages.php">User</a></li>
            <li><a id=logout >Logout</a></li>
        </ul>
    </nav>
    <div class="container" id="SongContainer">
        <div class="SearchBar">
            <div class="search-container">
                <form action="Main.php" method="get">
                    <input type="text" id="search" name="search" placeholder="Search for songs, artists, or albums..." value="<?php echo htmlspecialchars($searchKeyword ?? ''); ?>" autocomplete="off">
                    <div id="result" class="result"></div>
                </form>
            </div>

            <?php if ($searchResult && mysqli_num_rows($searchResult) > 0): ?>
            <?php elseif ($searchKeyword !== ''): ?>
                <p>No search result found</p>
            <?php endif; ?>
        </div>

        <div class="CurrentSong">
            <h2>Currently Playing</h2>
            <?php if ($currentSong): ?>
                <div class="SongContainer">
                    <div class="PrevSongItem">
                        <img src="Asset/MusikDefaultCover.svg" alt="Prev Song Cover">
                        <div class="PrevSongInfo">
                            <p class="SongTitle NamaLagu"><?php echo $prevSong['NamaLagu'] ?? '-'; ?></p>
                        </div>
                    </div>
                    <div class="CurrentSongItem">
                        <img src="<?php echo $currentSong['CoverLagu']; ?>" alt="Current Song Cover">
                        <div class="CurrentSongInfo">
                            <p class="SongTitle"><?php echo $currentSong['NamaLagu']; ?></p>
                            <p onclick="window.location.href='ArtistPage.php?NamaArtis=<?= $currentSong['NamaArtis'] ?>'" class="ArtistName"><?php echo $currentSong['NamaArtis']; ?></p>
                            
                            <!-- Audio Player -->
                            <?php if (!empty($currentSong['FileLagu'])): ?>
                                <audio id="audioPlayer" class="audio-player" controls style="display: none;">
                                    <source src="<?php echo $currentSong['FileLagu']; ?>" type="audio/mpeg">
                                    <source src="<?php echo $currentSong['FileLagu']; ?>" type="audio/wav">
                                    <source src="<?php echo $currentSong['FileLagu']; ?>" type="audio/ogg">
                                    Your browser does not support the audio element.
                                </audio>
                                
                                
                            <?php else: ?>
                                <p style="color: #999; font-size: 12px;">Audio file not available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="NextSongItem">
                        <img src="  Asset/MusikDefaultCover.svg" alt="Next Song Cover">
                        <div class="NextSongInfo">
                            <p class="SongTitle NamaLagu"  ><?php echo $nextSong['NamaLagu'] ?? '-'; ?></p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p>No Recent Song</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container" id="AudioPlayerContainer">
        <audio id="audio" src="<?php echo $currentSong['FileLagu']; ?>"></audio>
        <div class="audio-controls">
            <span class="time-display" id="currentTime">0:00</span>
            <div class="progress-container" id="progressContainer">
                <div class="progress-bar" id="progressBar"></div>
            </div>
            <span class="time-display" id="duration">0:00</span>
        </div>
    </div>

    <div class="PlayerControls">
        <div class="volume-container">
            <span>🔊</span>
            <input type="range" id="volumeSlider" class="volume-slider" min="0" max="100" value="70">
        </div>
        <div class="ControlButtons">
            <img src="Asset/prev.svg" alt="prev" class="ButtonPlayer" onclick="playPrevious()">
            <img src="Asset/Play.svg" alt="play" class="ButtonPlayer" id="playPauseBtn" onclick="togglePlayPause()">
            <img src="Asset/next.svg" alt="next" class="ButtonPlayer" onclick="playNext()">
        </div>
        <div class="QuePlayContainer">
            <form action="Main.php" method="post" class="favorite-form">
                <input name="currentSong" type="text" value="<?php echo htmlspecialchars($currentSong['id_musik'] ?? '') ?>" readonly style="display: none;">
                <button type="submit" value="<?php echo htmlspecialchars($_SESSION['id_user']) ?>" name="favoriteBtn" id="FavoriteBtn">
                    <img type="submit" src="Asset/Queue.svg" alt="Queue" class="ButtonPlayer" id="Favorite" >
                </button>
            </form>
            <img src="Asset/Playlisy.svg" alt="Playlist" class="ButtonPlayer" id="btn" onclick="OpenPopUp('playlist')">
        </div>
    </div>
    <div class="container" id="Genre">
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
        <form action="Main.php" method="post" class="FormContainer">
            <div class="popup-content">
                <input type="text" name="currentSong" id="currentSong" value="<?php echo $currentSong['id_musik'] ?? ''; ?>" readonly style="display: none;">
                <h2>Pilih Playlist</h2>
                <?php
                $userId = $_SESSION['id_user'] ?? null;
                $PlaylistQuery = "SELECT * FROM PLAYLIST WHERE id_user = '$userId' LIMIT 4";
                $PlaylistResult = mysqli_query($conn, $PlaylistQuery);

                if (mysqli_num_rows($PlaylistResult) > 0): 
                    while ($playlist = mysqli_fetch_assoc($PlaylistResult)): ?>
                        <div class="item">
                            <img src="<?php echo $playlist['image'] ?? 'Asset/ProfileDummy.png'; ?>" alt="cover album">
                            <a onclick="window.location.href='Playlist.php?id_playlist=<?= $playlist['id_playlist'] ?>'"><?php echo htmlspecialchars($playlist['NamaPlaylist']); ?></a>
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
                    <button type="button" onclick="OpenPopUp('AddPlaylist')">Tambah</button>
                <?php endif; ?>
                <button type="button" onclick="closePopUp()">Cancel</button>
                <button type="button" onclick="OpenPopUp('AddPlaylist')">Tambah</button>
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

    
    <!-- JAVA SCRIPT -->
    <script>
        let searchTimeout;
        let audioPlayer = null;
        let isPlaying = false;
        
        // Initialize audio player when page loads
        window.addEventListener('DOMContentLoaded', function() {
            audioPlayer = document.getElementById('audio');
            if (audioPlayer) {
                setupAudioPlayer();
            }
        });
        
        function setupAudioPlayer() {
            // Set initial volume
            audioPlayer.volume = 0.7;
            
            // Update progress bar
            audioPlayer.addEventListener('timeupdate', updateProgress);
            
            // Update duration when loaded
            audioPlayer.addEventListener('loadedmetadata', function() {
                document.getElementById('duration').textContent = formatTime(audioPlayer.duration);
            });
            
            // Handle play/pause state
            audioPlayer.addEventListener('play', function() {
                isPlaying = true;
                document.getElementById('playPauseBtn').src = 'Asset/Pause.svg';
            });
            
            audioPlayer.addEventListener('pause', function() {
                isPlaying = false;
                document.getElementById('playPauseBtn').src = 'Asset/Play.svg';
            });
            
            // Progress bar click
            document.getElementById('progressContainer').addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const width = rect.width;
                const clickPercent = clickX / width;
                const newTime = clickPercent * audioPlayer.duration;
                audioPlayer.currentTime = newTime;
            });
            
            // Volume control
            document.getElementById('volumeSlider').addEventListener('input', function() {
                audioPlayer.volume = this.value / 100;
            });
        }
        
        function updateProgress() {
            if (audioPlayer) {
                const progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
                document.getElementById('progressBar').style.width = progress + '%';
                document.getElementById('currentTime').textContent = formatTime(audioPlayer.currentTime);
                if (isNaN(audioPlayer.duration)) {
                    document.getElementById('duration').textContent = '0:00';
                } else {
                    document.getElementById('duration').textContent = formatTime(audioPlayer.duration);
                }
            }
        }
        
        function formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return mins + ':' + (secs < 10 ? '0' : '') + secs;
        }
        
        function togglePlayPause() {
            if (audioPlayer) {
                if (isPlaying) {
                    audioPlayer.pause();
                } else {
                    audioPlayer.play().catch(function(error) {
                        console.error('Error playing audio:', error);
                        alert('Lagi Kosong ni kayaknya.');
                    });
                }
            }
        }
        
        function playNext() {
            <?php if ($nextSong): ?>
                window.location.href = "Main.php?id_musik=<?php echo $nextSong['id_musik']; ?>";
            <?php else: ?>
                alert('Kosong ni, ga ada lagu selanjutnya');
            <?php endif; ?>
        }
        
        function playPrevious() {
            <?php if ($prevSong): ?>
                window.location.href = "Main.php?id_musik=<?php echo $prevSong['id_musik']; ?>";
            <?php else: ?>
                alert('Kosong ni, ga ada lagu sebelumnya');
            <?php endif; ?>
        }
        
        function OpenPopUp(popupType) {
            document.getElementById('blur-background').style.display = "block";

            if (popupType === 'playlist') {
                document.getElementById('PopUpPlaylist').style.display = "flex";
            } else if (popupType === 'queue') {
                document.getElementById('QueueForm').style.display = "flex";
            } else if (popupType == 'AddPlaylist') {
                document.getElementById('popupForm').style.display = "flex";
                document.getElementById('PopUpPlaylist').style.display = "none";
            }
        }

        function closePopUp() {
            document.getElementById('blur-background').style.display = "none";
            document.getElementById('PopUpPlaylist').style.display = "none";
            document.getElementById('popupForm').style.display = "none";
            document.getElementById('QueueForm').style.display = "none";
        }


        // Live search functionality
        document.getElementById('search').addEventListener('input', function () {
            const keyword = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (keyword !== '') {
                searchTimeout = setTimeout(() => {
                    fetch('livesearch.php?search=' + encodeURIComponent(keyword))
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('result').innerHTML = data;
                            document.getElementById('result').style.display = 'block';
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                        });
                }, 300); // Delay untuk mengurangi request
            } else {
                document.getElementById('result').innerHTML = '';
                document.getElementById('result').style.display = 'none';
            }
        });

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                document.getElementById('result').style.display = 'none';
            }
        });

        function selectSong(songName, artistName) {
            document.getElementById("search").value = songName + " - " + artistName;
            document.getElementById("result").style.display = "none";
            window.location.href = "Main.php?search=" + encodeURIComponent(songName);
        }
        
        function selectSongById(songId) {
            if (audioPlayer && !audioPlayer.paused) {
                audioPlayer.pause();
            }
            window.location.href = "Main.php?id_musik=" + encodeURIComponent(songId);
        }


        // Enter key search
        document.getElementById('search').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const keyword = this.value.trim();
                if (keyword !== '') {
                    window.location.href = "Main.php?search=" + encodeURIComponent(keyword);
                }
            }
        });

        document.getElementById('logout').addEventListener('click', function() {
            if (confirm('Are you sure you want to logout?')) {
                alert('You have successfully logged out.');
                window.location.href = 'Logout.php';
            } else {
                return false; // Prevent logout if user cancels
            }
        });

        const elements = document.querySelectorAll('.NamaLagu');
        elements.forEach(function(p) {
            const firstWord = p.textContent.trim().split(/\s+/)[0];
            p.textContent = firstWord;
        });

    </script>
</body>
</html>