<?php
declare(strict_types=1);

define('DB_PATH', dirname(__DIR__) . '/data/onion.db');
define('CACHE_TIME', 3600);

function getDatabaseConnection(): ?PDO {
    try {
        if (!is_dir(dirname(DB_PATH))) {
            mkdir(dirname(DB_PATH), 0755, true);
        }
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("CREATE TABLE IF NOT EXISTS submissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            url TEXT UNIQUE,
            title TEXT,
            description TEXT,
            category TEXT DEFAULT 'general',
            added_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        return $db;
    } catch (PDOException $e) {
        error_log("DB Connection Error: " . $e->getMessage());
        return null;
    }
}

function syncAhmiaData(): bool {
    $db = getDatabaseConnection();
    if (!$db) return false;

    $ch = curl_init("https://ahmia.fi/onions/");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'OnionSearch-Crawler/1.0',
        CURLOPT_TIMEOUT        => 30
    ]);
    
    $content = curl_exec($ch);
    curl_close($ch);

    if ($content) {
        preg_match_all('/[a-z2-7]{56}\.onion/', $content, $matches);
        $links = array_unique($matches[0]);

        if (!empty($links)) {
            $db->beginTransaction();
            try {
                $stmt = $db->prepare("INSERT OR IGNORE INTO submissions (url, category, title) VALUES (:url, :cat, :title)");
                foreach ($links as $link) {
                    $fullUrl = 'http://' . $link;
                    $stmt->execute([
                        ':url' => $fullUrl,
                        ':cat' => 'synced',
                        ':title' => 'Ahmia Synced Node'
                    ]);
                }
                $db->commit();
                return true;
            } catch (PDOException $e) {
                $db->rollBack();
                error_log("Sync Error: " . $e->getMessage());
                return false;
            }
        }
    }
    return false;
}

function searchLinks(string $query = ''): array {
    $db = getDatabaseConnection();
    if (!$db) return [];

    try {
        if (!empty($query)) {
            $stmt = $db->prepare("SELECT * FROM submissions WHERE url LIKE :q OR title LIKE :q OR description LIKE :q LIMIT 50");
            $stmt->execute([':q' => '%' . $query . '%']);
        } else {
            $stmt = $db->query("SELECT * FROM submissions ORDER BY id DESC LIMIT 50");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Search Error: " . $e->getMessage());
        return [];
    }
}
