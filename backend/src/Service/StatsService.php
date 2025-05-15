<?php
namespace App\Service;

use App\Infrastructure\Repository\StatsRepository;

/**
 * StatsService - 統計情報関連のビジネスロジックを提供するサービス
 */
class StatsService
{
    private $statsRepository;

    /**
     * コンストラクタ
     *
     * @param StatsRepository $statsRepository 統計リポジトリ
     */
    public function __construct(StatsRepository $statsRepository)
    {
        $this->statsRepository = $statsRepository;
    }

    /**
     * 視聴統計を記録する
     *
     * @param string $videoId 動画ID
     * @return bool 成功した場合はtrue
     */
    public function recordView(string $videoId): bool
    {
        return $this->statsRepository->recordView($videoId);
    }

    /**
     * クリック統計を記録する
     *
     * @param string $videoId 動画ID
     * @return bool 成功した場合はtrue
     */
    public function recordClick(string $videoId): bool
    {
        return $this->statsRepository->recordClick($videoId);
    }
}
