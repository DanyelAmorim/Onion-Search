<?php
require_once __DIR__ . '/includes/engine.php';

$query = $_GET['q'] ?? '';
$results = [];

if (!empty($query)) {
    $cleanQuery = trim(preg_replace('/[^a-zA-Z0-9\s._-]/', '', $query));
    if (!empty($cleanQuery)) {
        $results = searchLinks($cleanQuery);
    }
} else {
    $results = searchLinks('');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($query) ?> - Results</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><span>// </span>ONION SEARCH</h1>
        </header>

        <div class="search-wrapper">
            <form action="results.php" method="GET">
                <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search .onion links..." autofocus autocomplete="off">
                <button type="submit" class="search-btn"></button>
            </form>
        </div>

        <main class="results">
            <?php if (empty($results)): ?>
                <div class="status-msg">NO RESULTS FOUND FOR "<?= htmlspecialchars($query) ?>"</div>
            <?php else: ?>
                <?php foreach ($results as $row): 
                    $displayUrl = preg_replace('#^https?://#', '', rtrim($row['url'], '/'));
                    $category = $row['category'] ?? 'LINK';
                    $title = $row['title'] ?? $displayUrl;
                    $desc = $row['description'] ?? 'No description available.';
                ?>
                <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" rel="noopener noreferrer" class="card">
                    <div class="card-header">
                        <span class="tag"><?= htmlspecialchars(strtoupper($category)) ?></span>
                    </div>
                    <div class="link-url"><?= htmlspecialchars($displayUrl) ?></div>
                    <div class="link-title"><?= htmlspecialchars($title) ?></div>
                    <p style="color: #666; font-size: 0.85rem; margin-top: 5px; line-height: 1.4;">
                        <?= htmlspecialchars($desc) ?>
                    </p>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> Onion Search Project.</p>
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="?sync=1">Sync Database</a>
            </div>
            <div class="credits">
                <p>Inspired by <a href="https://github.com/BryanApolonio/" target="_blank" rel="noopener noreferrer">Bryan Apolonio</a></p>
            </div>
        </footer>
    </div>
</body>
</html>
