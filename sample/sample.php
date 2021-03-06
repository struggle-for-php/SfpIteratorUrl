<?php

use SfpIteratorUrl\IteratorUrl;

require_once __DIR__.'/../vendor/autoload.php';

class Repeat extends IteratorIterator
{
    public function current()
    {
        $str = str_repeat($this->getInnerIterator()->current(), 1024 * 1024);
        return $str;
    }
}

$iterator = new Repeat(new ArrayIterator(range('a', 'z')));

$fp = (new IteratorUrl)->open($iterator);

$body = new Zend\Diactoros\Stream($fp);

//fpassthru($body->detach()); // use 3.49 MB
//echo $body->__toString();   // use 28.49MB

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
