<?php
namespace App\Exception;

use Exception;

/**
 * ValidationException - バリデーションエラーの例外
 */
class ValidationException extends ApiException
{
    private $errors;
    
    /**
     * コンストラクタ
     *
     * @param array $errors バリデーションエラーの配列
     * @param string $message エラーメッセージ
     * @param Exception|null $previous 前の例外
     */
    public function __construct(array $errors = [], string $message = "Validation failed", Exception $previous = null)
    {
        parent::__construct($message, 400, $previous);
        $this->errors = $errors;
    }
    
    /**
     * バリデーションエラーの配列を取得する
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
