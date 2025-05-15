<?php
namespace App\Exception;

use Exception;

/**
 * NotFoundException - リソースが見つからない場合の例外
 */
class NotFoundException extends ApiException
{
    /**
     * コンストラクタ
     *
     * @param string $message エラーメッセージ
     * @param Exception|null $previous 前の例外
     */
    public function __construct(string $message = "Resource not found", Exception $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}
