<?php
namespace SfpIteratorUrl;

class StreamWrapper
{
    CONST CONTEXT_KEY_ITERATOR = 'iterator';
    
    public $context;
    
    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * did read length
     * 
     * @var int
     */
    private $length = 0;
    
    private $remains = '';

    public function stream_eof()
    {
        return !$this->iterator->valid();
    }
    
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        if (!preg_match('/^r[bt]?$/', $mode) || !$this->context) {
            return false;
        }
        
        $opt = stream_context_get_options($this->context);
        
        if (!is_array($opt[static::class]) ||
            !isset($opt[static::class][static::CONTEXT_KEY_ITERATOR]) ){
            return false;
        }
        
        $this->iterator = $opt[StreamWrapper::class]['iterator'];
        return true;
    }

    public function stream_read($count)
    {
        $contents = $this->remains;
        
        while ($this->iterator->valid() &&
                ($count - strlen($contents)) >= 0 ) {
            $content = $this->iterator->current();
            $contents .= $content;
            $this->length += strlen($content);
            $this->iterator->next();
        }
        
        $this->remains = substr($contents, $count);
        
        return substr($contents, 0, $count);
    }
    
    public function stream_seek($offset, $whence = SEEK_SET )
    {
        if ($whence != SEEK_END ) {
            // not support yet
            return false;
        }
        
        if ($whence === SEEK_CUR) {
            $this->stream_read($offset);
        }
        
        if ($offset > $this->length) {
            return false;
        }
        
        return true;
    }
    
    public function stream_stat()
    {
        return method_exists($this->iterator, __FUNCTION__) ? $this->iterator->{__FUNCTION__}() : []; 
    }
    
    public function stream_tell()
    {
        return $this->length;
    }
}