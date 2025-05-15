<?php
namespace App\Infrastructure\Repository;

use PDO;
use App\Model\Video;

/**
 * VideoRepository - 動画データの永続化を担当するリポジトリクラス
 */
class VideoRepository
{
    private $pdo;

    /**
     * コンストラクタ
     *
     * @param PDO $pdo PDOインスタンス
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * ランダムな動画を取得する
     *
     * @param int $limit 取得件数
     * @param string|null $genre ジャンル
     * @param string|null $tag タグ
     * @param string|null $actress 女優名
     * @return array 動画データの配列
     */
    public function getRandomVideos(int $limit = 3, ?string $genre = null, ?string $tag = null, ?string $actress = null): array
    {
        $params = [];
        $sql = "SELECT * FROM video_meta WHERE is_active = 1";
        
        if ($genre) {
            $sql .= " AND JSON_CONTAINS(genre, :genre, '$')";
            $params[':genre'] = json_encode($genre);
        }
        
        if ($tag) {
            $sql .= " AND JSON_CONTAINS(tags, :tag, '$')";
            $params[':tag'] = json_encode($tag);
        }
        
        if ($actress) {
            $sql .= " AND actress = :actress";
            $params[':actress'] = $actress;
        }
        
        $sql .= " ORDER BY RAND() LIMIT :limit";
        $params[':limit'] = $limit;
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === ':limit') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        $stmt->execute();
        
        $videos = [];
        while ($row = $stmt->fetch()) {
            $row['genre'] = json_decode($row['genre'], true);
            $row['tags'] = json_decode($row['tags'], true);
            $videos[] = $row;
        }
        
        return $videos;
    }

    /**
     * 動画を検索する
     *
     * @param string|null $actress 女優名
     * @param string|null $tag タグ
     * @param string|null $genre ジャンル
     * @param string|null $q 検索キーワード
     * @param int $limit 取得件数
     * @param int $offset オフセット
     * @return array 検索結果
     */
    public function searchVideos(?string $actress = null, ?string $tag = null, ?string $genre = null, ?string $q = null, int $limit = 10, int $offset = 0): array
    {
        $params = [];
        $sql = "SELECT * FROM video_meta WHERE is_active = 1";
        
        if ($actress) {
            $sql .= " AND actress = :actress";
            $params[':actress'] = $actress;
        }
        
        if ($tag) {
            $sql .= " AND JSON_CONTAINS(tags, :tag, '$')";
            $params[':tag'] = json_encode($tag);
        }
        
        if ($genre) {
            $sql .= " AND JSON_CONTAINS(genre, :genre, '$')";
            $params[':genre'] = json_encode($genre);
        }
        
        // 全文検索（簡易実装）
        if ($q) {
            $sql .= " AND (
                JSON_CONTAINS(tags, :q, '$') OR 
                JSON_CONTAINS(genre, :q, '$') OR 
                actress LIKE :q_like
            )";
            $params[':q'] = json_encode($q);
            $params[':q_like'] = '%' . $q . '%';
        }
        
        // 総件数取得用のクエリ
        $countSql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetch()['total'] ?? 0;
        
        // 結果取得用のクエリ
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === ':limit' || $key === ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        $stmt->execute();
        
        $videos = [];
        while ($row = $stmt->fetch()) {
            $row['genre'] = json_decode($row['genre'], true);
            $row['tags'] = json_decode($row['tags'], true);
            $videos[] = $row;
        }
        
        return [
            'videos' => $videos,
            'total' => $total
        ];
    }

    /**
     * マスターリスト（ジャンル、タグ、女優一覧）を取得する
     *
     * @return array マスターリスト
     */
    public function getMasterlists(): array
    {
        // ジャンル一覧取得
        $genreSql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(genre, '$[*]')) as genre_item 
                    FROM video_meta, JSON_TABLE(genre, '$[*]' COLUMNS(genre_value VARCHAR(255) PATH '$')) as jt 
                    WHERE is_active = 1";
        $genreStmt = $this->pdo->query($genreSql);
        $genres = [];
        while ($row = $genreStmt->fetch()) {
            $genres[] = $row['genre_item'];
        }
        
        // タグ一覧取得
        $tagSql = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(tags, '$[*]')) as tag_item 
                  FROM video_meta, JSON_TABLE(tags, '$[*]' COLUMNS(tag_value VARCHAR(255) PATH '$')) as jt 
                  WHERE is_active = 1";
        $tagStmt = $this->pdo->query($tagSql);
        $tags = [];
        while ($row = $tagStmt->fetch()) {
            $tags[] = $row['tag_item'];
        }
        
        // 女優一覧取得
        $actressSql = "SELECT DISTINCT actress FROM video_meta WHERE is_active = 1 AND actress IS NOT NULL";
        $actressStmt = $this->pdo->query($actressSql);
        $actresses = [];
        while ($row = $actressStmt->fetch()) {
            $actresses[] = $row['actress'];
        }
        
        return [
            'genres' => $genres,
            'tags' => $tags,
            'actresses' => $actresses
        ];
    }
}
