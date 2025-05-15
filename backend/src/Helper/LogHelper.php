<?php
namespace App\Helper;

/**
 * LogHelper - ログ出力に関するヘルパー関数を提供するクラス
 */
class LogHelper
{
    /**
     * ログファイルにメッセージを書き込む
     *
     * @param string $message ログメッセージ
     * @param string $level ログレベル（info, warning, error）
     * @return bool 成功した場合はtrue
     */
    public static function log(string $message, string $level = 'info'): bool
    {
        $logDir = '/home/yukiya1013/logs';
        $logFile = $logDir . '/tiktokfanza_' . date('Y-m-d') . '.log';
        
        // ログディレクトリが存在しない場合は作成
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[$timestamp] [$level] $message" . PHP_EOL;
        
        return (bool) file_put_contents($logFile, $formattedMessage, FILE_APPEND);
    }
    
    /**
     * 情報ログを出力する
     *
     * @param string $message ログメッセージ
     * @return bool 成功した場合はtrue
     */
    public static function info(string $message): bool
    {
        return self::log($message, 'info');
    }
    
    /**
     * 警告ログを出力する
     *
     * @param string $message ログメッセージ
     * @return bool 成功した場合はtrue
     */
    public static function warning(string $message): bool
    {
        return self::log($message, 'warning');
    }
    
    /**
     * エラーログを出力する
     *
     * @param string $message ログメッセージ
     * @return bool 成功した場合はtrue
     */
    public static function error(string $message): bool
    {
        return self::log($message, 'error');
    }
}
