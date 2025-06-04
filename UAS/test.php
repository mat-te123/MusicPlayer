<?php
session_start();
include_once("conn.php");

// Ambil data user dari session
$username = $_SESSION['UserName'];
$plan = $_SESSION['id_status'];

// Ambil info user dari database
$query = "SELECT UserName, NamaStatus, id_user, PhotoProfile 
          FROM user 
          JOIN status_user ON user.id_status = status_user.id_status 
          WHERE UserName='$username' AND user.id_status='$plan'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
$id_user = $data['id_user'];

// Tambah playlist jika form dikirim
if (isset($_POST['addPlaylistBtn'])) {
    $playlist_name = $_POST['playlist_name'];
    $default_cover = "Asset/DefaultCover.svg";

    // Cek apakah user sudah memiliki playlist
    $playlistQuery = "select * from playlist where id_user = '$id_user'";
    $playlistResult = mysqli_query($conn, $playlistQuery);

    if (mysqli_num_rows($playlistResult) > 0) {
        // Ambil id_playlist terakhir
        $lastPlaylistQuery = "SELECT id_playlist FROM playlist ORDER BY id_playlist DESC LIMIT 1";
        $lastPlaylistResult = mysqli_query($conn, $lastPlaylistQuery);
        $lastPlaylist = mysqli_fetch_assoc($lastPlaylistResult);
        $lastId = $lastPlaylist['id_playlist'];
        
        // Ambil angka dan tambahkan
        $num = intval(substr($lastId, 2)) + 1;
        $newId = "PL" . str_pad($num, 2, "0", STR_PAD_LEFT);
    } else {
        $newId = 'PL01'; // ID awal jika belum ada playlist
    }
    $insert = "INSERT INTO playlist (id_playlist,NamaPlaylist, id_user, image) VALUES ('$newId', '$playlist_name','$id_user', '$default_cover')";
    $result = mysqli_query($conn, $insert);
    if (!$result) {
        echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
    } else {
        echo "<script>alert('Playlist berhasil ditambahkan!');</script>";
    }
    header("Location: UserPages.php");
    exit;
}

if (isset($_POST['changeProfilePictureBtn'])) {
    // Cek apakah file diupload
    if (isset($_FILES['PhotoProfile']) && $_FILES['PhotoProfile']['error'] == 0) {
        $fileTmpPath = $_FILES['PhotoProfile']['tmp_name'];
        $fileName = $_FILES['PhotoProfile']['name'];

        
        // Tentukan direktori penyimpanan
        $uploadFileDir = 'uploads/';
        $newFileName = uniqid() . '.' . basename($fileName);
        $dest_path = $uploadFileDir . $fileName;
        // Pindahkan file ke direktori tujuan
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Update foto profil di database
            $updateQuery = "UPDATE user SET PhotoProfile='$dest_path' WHERE id_user='$id_user'";
            if (mysqli_query($conn, $updateQuery)) {
                $query = "SELECT PhotoProfile FROM user WHERE id_user='$id_user'";
                $result = mysqli_query($conn, $query);
                $newpic = mysqli_fetch_assoc($result);
                $data['PhotoProfile'] = $newpic['PhotoProfile'];
                echo "<script>alert('Foto profil berhasil diubah!');</script>";
            } else {
                echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
            }
        } else {
            echo "<script>alert('Gagal mengunggah file.');</script>";
        }
    }
}
    // Jika tidak ada file yang diupload, tetap tampilkan foto profil lama
$data['PhotoProfile'] = !empty($data['PhotoProfile']) ? $data['PhotoProfile'] : 'uploads/DefaultProfile.svg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Profile - <?php echo htmlspecialchars($data['UserName']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-bg: #0a0a0a;
            --secondary-bg: #1a1a1a;
            --card-bg: #252525;
            --accent-color: #6366f1;
            --accent-hover: #4f46e5;
            --text-primary: #ffffff;
            --text-secondary: #a1a1aa;
            --border-color: #333333;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-accent: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--primary-bg);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Background Animation */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(147, 51, 234, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
            z-index: -1;
            animation: backgroundShift 20s ease-in-out infinite;
        }

        @keyframes backgroundShift {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            backdrop-filter: blur(20px);
            background: rgba(37, 37, 37, 0.8);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 12px 24px;
            box-shadow: var(--shadow-lg);
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 8px;
            align-items: center;
        }

        nav ul li a {
            text-decoration: none;
            color: var(--text-secondary);
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        nav ul li a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-primary);
            transition: left 0.3s ease;
            z-index: -1;
        }

        nav ul li a:hover,
        nav ul li a.active {
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        nav ul li a:hover::before,
        nav ul li a.active::before {
            left: 0;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 20px 40px;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        /* Profile Section */
        .profile-section {
            background: var(--card-bg);
            border-radius: 32px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border-color);
        }

        .profile-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .profile-content {
            display: flex;
            align-items: center;
            gap: 40px;
            position: relative;
            z-index: 2;
        }

        .profile-photo-container {
            position: relative;
            flex-shrink: 0;
        }

        .profile-photo {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid transparent;
            background: var(--gradient-primary);
            padding: 4px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 32px rgba(99, 102, 241, 0.3);
        }

        .profile-photo:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 48px rgba(99, 102, 241, 0.4);
        }

        .edit-photo-btn {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            background: var(--gradient-accent);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(245, 87, 108, 0.4);
        }

        .edit-photo-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 24px rgba(245, 87, 108, 0.6);
        }

        .profile-info h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 8px;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .profile-info .username {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .plan-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gradient-accent);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 16px rgba(245, 87, 108, 0.3);
        }

        /* Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .add-btn {
            width: 48px;
            height: 48px;
            background: var(--gradient-primary);
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
        }

        .add-btn:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 6px 24px rgba(99, 102, 241, 0.6);
        }

        /* Playlist Section */
        .playlist-section,
        .favorites-section {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 32px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
        }

        .playlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 24px;
        }

        .playlist-item {
            background: var(--secondary-bg);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .playlist-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .playlist-item:hover {
            transform: translateY(-8px);
            border-color: var(--accent-color);
            box-shadow: 0 12px 32px rgba(99, 102, 241, 0.2);
        }

        .playlist-item:hover::before {
            transform: scaleX(1);
        }

        .playlist-item img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            margin-bottom: 12px;
            transition: transform 0.3s ease;
        }

        .playlist-item:hover img {
            transform: scale(1.1);
        }

        .playlist-item h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Favorites Section */
        .favorites-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .favorite-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 16px;
            background: var(--secondary-bg);
            border-radius: 16px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .favorite-item:hover {
            background: var(--card-bg);
            border-color: var(--accent-color);
            transform: translateX(8px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.1);
        }

        .favorite-item img {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .favorite-info h4 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .favorite-info p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            opacity: 0.8;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
        }

        .modal {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 32px;
            max-width: 400px;
            width: 90%;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-xl);
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal {
            transform: scale(1);
        }

        .modal h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: center;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-input {
            width: 100%;
            padding: 16px;
            background: var(--secondary-bg);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--secondary-bg);
            color: var(--text-primary);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 80px 16px 20px;
            }

            .profile-content {
                flex-direction: column;
                text-align: center;
                gap: 24px;
            }

            .profile-info h1 {
                font-size: 2rem;
            }

            .playlist-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 16px;
            }

            .section-title {
                font-size: 1.5rem;
            }

            nav {
                top: 10px;
                left: 10px;
                right: 10px;
                transform: none;
                border-radius: 20px;
            }

            nav ul {
                gap: 4px;
            }

            nav ul li a {
                padding: 8px 12px;
                font-size: 12px;
            }
        }

        /* Loading Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .loading {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <ul>
            <li><a href="Main.php">🏠 Home</a></li>
            <li><a href="Queue.php">🎵 Queue</a></li>
            <li><a href="Plan.html">💎 Plan</a></li>
            <li><a href="UserPages.php" class="active">👤 User</a></li>
        </ul>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-content">
                <div class="profile-photo-container">
                    <img src="<?php echo htmlspecialchars($data['PhotoProfile']); ?>" alt="Profile Picture" class="profile-photo">
                    <button class="edit-photo-btn" onclick="openModal('photoModal')">✏️</button>
                </div>
                <div class="profile-info">
                    <h1>Profile</h1>
                    <div class="username"><?php echo htmlspecialchars($data['UserName']); ?></div>
                    <div class="plan-badge">
                        ⭐ <?php echo htmlspecialchars($data['NamaStatus']); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Playlist Section -->
        <div class="playlist-section">
            <div class="section-header">
                <h2 class="section-title">My Playlists</h2>
                <button class="add-btn" onclick="openModal('playlistModal')">+</button>
            </div>
            <div class="playlist-grid" id="playlistGrid">
                <?php
                // Ambil semua playlist dari database
                $playlistQuery = "SELECT * FROM playlist WHERE id_user = '$id_user' order by id_playlist ASC LIMIT 5";
                $playlistResult = mysqli_query($conn, $playlistQuery);
                if (!$playlistResult) {
                    echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
                }
                ?>
                <!-- Cek apakah ada playlist -->
                <?php if (mysqli_num_rows($playlistResult) === 0): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">🎵</div>
                        <h3>No Playlists Yet</h3>
                        <p>Create your first playlist to get started</p>
                    </div>
                <?php else: ?>
                    <?php while ($playlist = mysqli_fetch_assoc($playlistResult)): ?>
                        <div class="playlist-item">
                            <img src="<?php echo htmlspecialchars($playlist['image']); ?>" alt="Playlist Cover">
                            <h3><?php echo htmlspecialchars($playlist['NamaPlaylist']); ?></h3>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Favorites Section -->
        <div class="favorites-section">
            <div class="section-header">
                <h2 class="section-title">Favorite Songs</h2>
            </div>
            <div class="favorites-list" id="favoritesList">
                <?php
                $favoriteSongQuery = "SELECT musik.NamaLagu, artis.NamaArtis, musik.CoverLagu 
                FROM favorit 
                join musik on favorit.id_musik = musik.id_musik
                join artis on musik.id_artis = artis.id_artis 
                WHERE id_user = '$id_user' 
                order by favorit.id_musik asc 
                limit 5";
                $favoriteSongResult = mysqli_query($conn, $favoriteSongQuery);
                if (!$favoriteSongResult) {
                    echo "<script>alert('Error: " . mysqli_error($conn) . "')</script>";
                }
                ?>
                <?php if (mysqli_num_rows($favoriteSongResult) === 0): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">❤️</div>
                        <h3>No Favorite Songs</h3>
                        <p>Start liking songs to see them here</p>
                    </div>
                <?php else: ?>
                    <?php while ($favoriteSong = mysqli_fetch_assoc($favoriteSongResult)): ?>
                        <div class="favorite-item">
                            <img src="<?php echo htmlspecialchars($favoriteSong['CoverLagu']); ?>" alt="Song Cover">
                            <div class="favorite-info">
                                <h4><?php echo htmlspecialchars($favoriteSong['NamaLagu']); ?></h4>
                                <p><?php echo htmlspecialchars($favoriteSong['NamaArtis']); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Playlist Modal -->
    <div class="modal-overlay" id="playlistModal">
        <div class="modal">
            <h2>Create New Playlist</h2>
            <form method="POST" id="playlistForm">
                <div class="form-group">
                    <input type="text" name="playlist_name" class="form-input" placeholder="Playlist Name" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('playlistModal')">Cancel</button>
                    <button type="submit" name="addPlaylistBtn" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Photo Modal -->
    <div class="modal-overlay" id="photoModal">
        <div class="modal">
            <h2>Change Profile Photo</h2>
            <form method="POST" enctype="multipart/form-data" id="photoForm">
                <div class="form-group">
                    <input type="file" name="PhotoProfile" class="form-input" accept="image/*" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('photoModal')">Cancel</button>
                    <button type="submit" name="changeProfilePictureBtn" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Add hover effects and animations
        document.addEventListener('DOMContentLoaded', () => {
            // Animate elements on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all sections
            document.querySelectorAll('.playlist-section, .favorites-section').forEach(section => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(section);
            });
        });

        // Add dynamic interactions
        document.querySelectorAll('.playlist-item, .favorite-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.transform = item.classList.contains('playlist-item') ? 'translateY(-8px)' : 'translateX(8px)';
            });
            
            item.addEventListener('mouseleave', () => {
                item.style.transform = 'translateY(0) translateX(0)';
            });
        });
    </script>
</body>
</html>