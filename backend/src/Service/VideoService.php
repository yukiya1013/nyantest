<?php
namespace App\Service;

use App\Infrastructure\Repository\VideoRepository;

/**
 * VideoService - 動画関連のビジネスロジックを提供するサービス
 */
class VideoService
{
    private $videoRepository;
    private $affiliateId;

    /**
     * コンストラクタ
     *
     * @param VideoRepository $videoRepository 動画リポジトリ
     * @param string $affiliateId アフィリエイトID
     */
    public function __construct(VideoRepository $videoRepository, string $affiliateId)
    {
        $this->videoRepository = $videoRepository;
        $this->affiliateId = $affiliateId;
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
        $videos = $this->videoRepository->getRandomVideos($limit, $genre, $tag, $actress);
        return $this->processVideos($videos);
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
        $result = $this->videoRepository->searchVideos($actress, $tag, $genre, $q, $limit, $offset);
        $videos = $result['videos'] ?? [];
        $total = $result['total'] ?? 0;
        
        return [
            'videos' => $this->processVideos($videos),
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * マスターリスト（ジャンル、タグ、女優一覧）を取得する
     *
     * @return array マスターリスト
     */
    public function getMasterlists(): array
    {
        return $this->videoRepository->getMasterlists();
    }

    /**
     * 動画データを処理する（アフィリエイトIDの付与など）
     *
     * @param array $videos 動画データの配列
     * @return array 処理後の動画データ
     */
    private function processVideos(array $videos): array
    {
        foreach ($videos as &$video) {
            // 購入URLにアフィリエイトIDを付与
            if (isset($video['purchase_url']) && !empty($video['purchase_url'])) {
                $video['purchase_url'] = $this->appendAffiliateId($video['purchase_url']);
            }
            
            // 動画ファイルのパスを設定
            if (isset($video['ulid']) && !empty($video['ulid'])) {
                $video['video_url'] = '/videos/' . $video['ulid'] . '.mp4';
                $video['thumbnail_url'] = '/videos/' . $video['ulid'] . '.jpg';
            }
        }
        
        return $videos;
    }

    /**
     * URLにアフィリエイトIDを付与する
     *
     * @param string $url 元のURL
     * @return string アフィリエイトID付きのURL
     */
    private function appendAffiliateId(string $url): string
    {
        $separator = (strpos($url, '?') !== false) ? '&' : '?';
        
        // すでにaffiパラメータがある場合は置換、なければ追加
        if (preg_match('/[?&]affi=([^&]*)/', $url)) {
            return preg_replace('/([?&]affi=)[^&]*/', '$1' . $this->affiliateId, $url);
        } else {
            return $url . $separator . 'affi=' . $this->affiliateId;
        }
    }
}
