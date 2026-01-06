<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Menu | The Obsidian</title>
    <link rel="stylesheet" href="../hotelpage.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #3d3939ff;
            color: #fff;
        }

        .menu-container {
            max-width: 1100px;
            margin: 60px auto;
            padding: 20px;
            text-align: center;
        }

        .menu-container h1 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 70px;
            font-size: 100px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 40px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .menu-grid img {
            width: 100%;
            border-radius: 16px;
            transition: transform 0.3s ease;
        }

        .menu-grid img:hover {
            transform: scale(1.05);
        }

        /* === BACK BUTTON (MATCH FIRST PAGE) === */
        .below-header-back {
            max-width: 1300px;
            margin: 90px auto 40px;
            padding: 0 30px;
        }

        .back-btn {
            font-family: var(--font-sans);
            font-size: 13px;
            font-weight: 400;
            color: white;
            text-decoration: none;
            display: inline-block;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 6px;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .back-btn:hover {
            color: var(--color-accent);
            transform: translateX(-4px);
        }
    </style>
</head>

<body>

    <!-- Back button LEFT, below header -->
    <div class="below-header-back">
        <a href="../hotel.php" class="back-btn">← Back to Main</a>
    </div>

    <div class="menu-container">
        <h1>Our Menu</h1>

        <div class="menu-grid">
            <img src="1.png" alt="Menu Page 1">
            <img src="2.png" alt="Menu Page 2">
            <img src="3.png" alt="Menu Page 3">
            <img src="4.png" alt="Menu Page 4">
        </div>
    </div>

</body>
</html>
