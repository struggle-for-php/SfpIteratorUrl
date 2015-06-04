SfpIteratorUrl
==============

A streamWrapper for Iterator.

## Why ?

To avoid entire buffering.

## Usage (Yeah! memory usage!)

```php
class Bigsize extends IteratorIterator
{
    public function current()
    {
        $str = str_repeat($this->getInnerIterator()->current(), 8192);
        return $str;
    }
}

$iterator = new Bigsize(new ArrayIterator(range(1, 100)));
$fp = (new IteratorUrl)->open($iterator);
$body = new Zend\Diactoros\Stream($fp);

// emit
fpassthru($body->detach());

echo formatBytes(memory_get_usage());  // 278.97 KB <-- look
// echo $body->__toString();           // 1.75MB
```