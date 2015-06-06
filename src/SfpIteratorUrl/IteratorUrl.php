<?php

namespace SfpIteratorUrl;

use Iterator;

class IteratorUrl
{
    protected $protocol;

    public function __construct($protocol = 'sfp.iterator')
    {
        $this->protocol = $protocol;
    }

    protected function register()
    {
        if (!in_array($this->protocol, stream_get_wrappers())) {
            stream_wrapper_register($this->protocol, StreamWrapper::class);
        }
    }

    public function open(Iterator $iterator, $register_auto = true)
    {
        ($register_auto) ?: $this->register();
        $iterator->rewind();

        $ctx = stream_context_create([
            StreamWrapper::class => [
                StreamWrapper::CONTEXT_KEY_ITERATOR => $iterator,
            ],
        ]);

        $fp = fopen($this->protocol.'://', 'r', false, $ctx);

        return $fp;
    }
}
