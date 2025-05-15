<?php
namespace App\Helper;

/**
 * UlidHelper - ULIDに関するヘルパー関数を提供するクラス
 */
class UlidHelper
{
    /**
     * ULIDが有効かどうかを検証する
     *
     * @param string $ulid 検証するULID
     * @return bool 有効な場合はtrue
     */
    public static function isValid(string $ulid): bool
    {
        // ULIDは26文字の英数字（Crockford's Base32）
        return (bool) preg_match('/^[0-9A-HJKMNP-TV-Z]{26}$/i', $ulid);
    }
    
    /**
     * ファイル名からULIDを抽出する
     *
     * @param string $filename ファイル名
     * @return string|null 抽出されたULID、または無効な場合はnull
     */
    public static function extractFromFilename(string $filename): ?string
    {
        $basename = basename($filename);
        $parts = explode('.', $basename);
        $ulid = $parts[0];
        
        return self::isValid($ulid) ? $ulid : null;
    }
}
