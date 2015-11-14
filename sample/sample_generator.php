<?php

use SfpIteratorUrl\IteratorUrl;

require_once __DIR__.'/../vendor/autoload.php';

// よく見かける アクション内でのecho
// ob_start
// foreach (range('a', 'z') as $char) {
//     echo str_repeat($char, 1024 * 1024);
//     ob_flush();
// }

$gen = function () {    
    foreach (range('a', 'z') as $char) {
        yield str_repeat($char, 1024 * 1024);
    }
};


$iterator = $gen();

$fp = (new IteratorUrl)->open($iterator);

$body = new Zend\Diactoros\Stream($fp);
fpassthru($body->detach());
echo $body->__toString();                          
echo "\n";
echo "memory used ",(formatBytes(memory_get_peak_usage()));  

// http://qiita.com/suin/items/0090ab167bbdb3d77181
function formatBytes($bytes, $precision = 2, array $units = null)
{
    if ( abs($bytes) < 1024 )
    {
        $precision = 0;
    }

    if ( is_array($units) === false )
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    }

    if ( $bytes < 0 )
    {
        $sign = '-';
        $bytes = abs($bytes);
    }
    else
    {
        $sign = '';
    }

    $exp   = floor(log($bytes) / log(1024));
    $unit  = $units[$exp];
    $bytes = $bytes / pow(1024, floor($exp));
    $bytes = sprintf('%.'.$precision.'f', $bytes);
    return $sign.$bytes.' '.$unit;
}
