<?php
include_once("conn.php");

$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($searchKeyword !== '') {
    // Escape the search keyword to prevent SQL injection
    $searchKeyword = mysqli_real_escape_string($conn, $searchKeyword);
    
    // Query untuk mencari lagu dan artis
    $query = "SELECT musik.*, artis.NamaArtis 
              FROM musik 
              JOIN artis ON musik.id_artis = artis.id_artis 
              WHERE musik.NamaLagu LIKE '%$searchKeyword%' 
                 OR artis.NamaArtis LIKE '%$searchKeyword%' 
              ORDER BY musik.NamaLagu ASC 
              LIMIT 8";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $songName = htmlspecialchars($row['NamaLagu']);
            $artistName = htmlspecialchars($row['NamaArtis']);
            $coverImage = $row['CoverLagu'] ?? 'Asset/MusikDefaultCover.svg';
            $songId = $row['id_musik'];
            
            echo "<div class='search-item' onclick='selectSongById($songId)'>
                    <img src='$coverImage' alt='Cover' onerror=\"this.src='Asset/MusikDefaultCover.svg'\">
                    <div class='search-item-info'>
                        <h4>$songName</h4>
                        <p>$artistName</p>
                    </div>
                  </div>";
        }
    } else {
        echo "<div class='search-item' style='cursor: default;'>
                <div class='search-item-info'>
                    <h4 style='color: #999;'>No results found</h4>
                </div>
              </div>";
    }
}
?>