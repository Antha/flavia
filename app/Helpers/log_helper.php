<?php
if (!function_exists('write_custom_log')) {
    function write_custom_log(string $message, string $filename = 'custom-log.txt')
    {
        $filePath = WRITEPATH . 'logs/' . $filename;
        $time = date('Y-m-d H:i:s');
        $log = "[{$time}] {$message}" . PHP_EOL;

        file_put_contents($filePath, $log, FILE_APPEND);
    }
}
?>