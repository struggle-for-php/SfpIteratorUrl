<?php

use SfpIteratorUrl\IteratorUrl;

require_once __DIR__.'/../vendor/autoload.php';

class Repeat extends IteratorIterator
{
    public function current()
    {
        $str = str_repeat($this->getInnerIterator()->current(), 8192);
        return $str;
    }
}

$iterator = new Repeat(new ArrayIterator(range(1, 100)));

$fp = (new IteratorUrl)->open($iterator);

$body = new Zend\Diactoros\Stream($fp);
fpassthru($body->detach());
echo "<hr>";
echo "echo ", formatBytes(8192 * 100), "<br>";          // echo 800.00 KB
echo "memory used ",(formatBytes(memory_get_usage()));  // memory used 278.97 KB
// echo $body->__toString();                            // will be use 1.75MB

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