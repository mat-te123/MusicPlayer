<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="PlaylistStyle.css">
    <title>UserPage</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="Main.php">Home</a></li>
            <li><a href="Queue.php">Queue</a></li>
            <li><a href="Playlist.php">Playlist</a></li>
            <li><a href="UserPages.php">User</a></li>
        </ul>
    </nav>
    <div class="container">
        <div class="PlaylistName">
            <div class="PhotoPlaylist">
                <img src="Asset/ProfileDummy.png" alt="Playlist Picture">
            </div class=>
            <div class="InfoPlaylist">
                <div class="InfoPlaylist_Name">
                    <h2>
                        Playlist Name
                    </h2>
                    <p>
                        Playlist Description
                    </p>
                </div>
            </div>
        </div>
        <img src="Asset/Play.svg" alt="play" class="PlayButton">
        <div class="ListSong">
            <h2>
                Daftar Lagu
            </h2>
            <div class="ListSongItem">
                    <img src="Asset/ProfileDummy.png" alt="Song Cover">
                    <a href="SongName" class="SongName">
                        Song Name
                    </a>
                    <a href="SongName" class="ArtistName">
                        Artist Name
                    </a>
            </div>
        </div>
    </div>
    
</body>
</html>