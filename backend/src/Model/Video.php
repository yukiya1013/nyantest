<?php
namespace App\Model;

/**
 * Video - 動画データのモデルクラス
 */
class Video
{
    public $id;
    public $ulid;
    public $genre;
    public $tags;
    public $actress;
    public $purchase_url;
    public $is_active;
    public $created_at;
    
    /**
     * コンストラクタ
     *
     * @param array $data 動画データ
     */
    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->ulid = $data['ulid'] ?? null;
        $this->genre = $data['genre'] ?? [];
        $this->tags = $data['tags'] ?? [];
        $this->actress = $data['actress'] ?? null;
        $this->purchase_url = $data['purchase_url'] ?? null;
        $this->is_active = $data['is_active'] ?? 1;
        $this->created_at = $data['created_at'] ?? null;
    }
    
    /**
     * 配列に変換する
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ulid' => $this->ulid,
            'genre' => $this->genre,
            'tags' => $this->tags,
            'actress' => $this->actress,
            'purchase_url' => $this->purchase_url,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at
        ];
    }
}
