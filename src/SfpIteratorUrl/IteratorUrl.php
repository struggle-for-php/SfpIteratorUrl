<?php
namespace SfpIteratorUrl;

use Iterator;

class IteratorUrl
{
    protected $protocol;
    
    public function __construct($protocol = 'SfpIteratorUrl')
    {
        $this->protocol = $protocol;
    }
    
    protected function register()
    {
        if (!in_array($this->protocol, stream_get_wrappers())) {
            stream_wrapper_register($this->protocol, StreamWrapper::class);
        }
    }
    
    public function open(Iterator $iterator)
    {
        $this->register();
        $iterator->rewind();
        
        $ctx = stream_context_create([
            StreamWrapper::class => [
                StreamWrapper::CONTEXT_KEY_ITERATOR => $iterator
            ]
        ]);
        
        $fp = fopen($this->protocol.'://', 'r', false, $ctx);
        return $fp;
    }
}