<?php
namespace App\Exception;

use Exception;

/**
 * ApiException - API処理中に発生する例外の基底クラス
 */
class ApiException extends Exception
{
    protected $statusCode;
    
    /**
     * コンストラクタ
     *
     * @param string $message エラーメッセージ
     * @param int $statusCode HTTPステータスコード
     * @param Exception|null $previous 前の例外
     */
    public function __construct(string $message = "API Error", int $statusCode = 500, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
    }
    
    /**
     * HTTPステータスコードを取得する
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
