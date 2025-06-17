<?php
include_once("conn.php");
include_once("UserData.php");

$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$currentSong = null;
$nextSong = null;
$prevSong = null;
$searchResult = null;

if ($searchKeyword !== '') {
    // Query pencarian
    $query = "SELECT musik.*, artis.NamaArtis 
              FROM musik 
              JOIN artis ON musik.id_artis = artis.id_artis 
              WHERE musik.NamaLagu LIKE '%$searchKeyword%' OR artis.NamaArtis LIKE '%$searchKeyword%' 
              LIMIT 1";
    $result = mysqli_query($conn, $query);
    $currentSong = mysqli_fetch_assoc($result);

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