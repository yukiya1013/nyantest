<?php
namespace App\Infrastructure\Repository;

use PDO;

/**
 * StatsRepository - 統計情報の永続化を担当するリポジトリクラス
 */
class StatsRepository
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
     * 視聴統計を記録する
     *
     * @param string $videoId 動画ID
     * @return bool 成功した場合はtrue
     */
    public function recordView(string $videoId): bool
    {
        $sql = "INSERT INTO view_stats (video_id) VALUES (:video_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':video_id', $videoId);
        return $stmt->execute();
    }

    /**
     * クリック統計を記録する
     *
     * @param string $videoId 動画ID
     * @return bool 成功した場合はtrue
     */
    public function recordClick(string $videoId): bool
    {
        $sql = "INSERT INTO click_stats (video_id) VALUES (:video_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':video_id', $videoId);
        return $stmt->execute();
    }
}
