<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Onion Search</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><span>// </span>ONION SEARCH</h1>
            <p class="subtitle">Secure Anonymous Link Aggregator</p>
        </header>

        <div class="search-wrapper">
            <form action="results.php" method="GET">
                <input type="text" name="q" placeholder="Search .onion links..." autofocus autocomplete="off">
                <button type="submit" class="search-btn"></button>
            </form>
        </div>

        <footer>
            <p>&copy; <?= date('Y') ?> Onion Search Project.</p>
            <div class="credits">
                <p>Inspired by <a href="https://github.com/BryanApolonio/" target="_blank" rel="noopener noreferrer">Bryan Apolonio</a></p>
            </div>
        </footer>
    </div>
</body>
</html>
