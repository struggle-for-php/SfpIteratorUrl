SfpIteratorUrl
==============

A streamWrapper for Iterator.

## Why ?

To avoid entire buffering

## Usage (Yeah! memory usage!)

```php
$fp = (new IteratorUrl)->open($iterator);
$body = new Zend\Diactoros\Stream($fp);

// emit
fpassthru($body->detach());

echo formatBytes(8192 * 100);          // echo 800.00 KB
echo formatBytes(memory_get_usage());  // memory used 278.97 KB
// echo $body->__toString();           // will be use 1.75MB
```