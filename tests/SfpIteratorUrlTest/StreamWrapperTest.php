<?php

namespace SfpIteratorUrlTest;

use PHPUnit_Framework_TestCase;
use ArrayIterator;
use SfpIteratorUrl\IteratorUrl;

class StreamWrapperTest extends PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $chrs = range('a', 'z');
        $iterator = new ArrayIterator($chrs);
        $fp = (new IteratorUrl())->open($iterator);

        $this->assertEquals(implode('', $chrs), stream_get_contents($fp, -1, 0));
    }
}
