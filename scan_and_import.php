<?php
require __DIR__ . '/../backend/vendor/autoload.php';

use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../backend');
$dotenv->load();

// Database connection
$dbHost = 'localhost'; // Or your DB host from .env if different
$dbName = $_ENV['DB_NAME'] ?? 'yukiya1013_tiktokfanza'; // Fallback if not in .env, though it should be
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("DB Connection Error: " . $e->getMessage() . "\n");
}

$videoFolder = __DIR__ . '/../videofolder';
$affiliateId = $_ENV['AFFILIATE_ID'];

$jsonFiles = glob($videoFolder . '/*.json');

if (empty($jsonFiles)) {
    echo "No JSON files found in {$videoFolder}\n";
    exit;
}

echo "Found " . count($jsonFiles) . " JSON files. Starting import/update...\n";

foreach ($jsonFiles as $jsonFile) {
    $jsonData = file_get_contents($jsonFile);
    $videoData = json_decode($jsonData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding JSON from file: {$jsonFile}. Error: " . json_last_error_msg() . "\n";
        continue;
    }

    // Extract data, assuming JSON structure from user's screenshot
    // Example: {"id":"mid00906","genre":[],"tags":["お風呂場なら..."],"purchase_url":"https://..."}
    $id = $videoData['id'] ?? null;
    $genre = json_encode($videoData['genre'] ?? []);
    $tags = json_encode($videoData['tags'] ?? []);
    $actress = $videoData['actress'] ?? null; // Assuming 'actress' might be in JSON
    $purchaseUrl = $videoData['purchase_url'] ?? null;

    if (!$id) {
        echo "Skipping file {$jsonFile} due to missing 'id'.\n";
        continue;
    }

    // Add affiliate ID if not present
    if ($purchaseUrl && strpos($purchaseUrl, 'affi=') === false) {
        $separator = (strpos($purchaseUrl, '?') === false) ? '?' : '&';
        $purchaseUrl .= $separator . 'affi=' . $affiliateId;
    }

    // UPSERT logic
    $sql = "INSERT INTO video_meta (id, genre, tags, actress, purchase_url, is_active, created_at)
            VALUES (:id, :genre, :tags, :actress, :purchase_url, 1, CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE
            genre = VALUES(genre),
            tags = VALUES(tags),
            actress = VALUES(actress),
            purchase_url = VALUES(purchase_url),
            is_active = 1;" // Ensure is_active is set to 1 on update as well
            ;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':tags', $tags);
        $stmt->bindParam(':actress', $actress);
        $stmt->bindParam(':purchase_url', $purchaseUrl);
        $stmt->execute();
        echo "Processed: {$id} from {$jsonFile}\n";
    } catch (PDOException $e) {
        echo "Error processing {$id} from {$jsonFile}: " . $e->getMessage() . "\n";
    }
}

echo "Import/update process completed.\n";

// fix_affiliate.php logic can be integrated here or kept separate.
// For now, the above scan_and_import already handles adding affiliate ID to new/updated entries.
// If a separate script is strictly needed to *only* fix existing entries without affi=, it would be:

/*
echo "\nStarting affiliate ID fix for existing entries without it...\n";
$sqlFix = "UPDATE video_meta SET purchase_url = CONCAT(purchase_url, CASE WHEN LOCATE('?', purchase_url) > 0 THEN '&' ELSE '?' END, 'affi=', :affiliate_id) WHERE purchase_url IS NOT NULL AND purchase_url != '' AND LOCATE('affi=', purchase_url) = 0;";
try {
    $stmtFix = $pdo->prepare($sqlFix);
    $stmtFix->bindParam(':affiliate_id', $affiliateId);
    $count = $stmtFix->execute();
    echo "Affiliate ID fix applied to " . $stmtFix->rowCount() . " entries.\n";
} catch (PDOException $e) {
    echo "Error fixing affiliate IDs: " . $e->getMessage() . "\n";
}
echo "Affiliate ID fix process completed.\n";
*/

?>

