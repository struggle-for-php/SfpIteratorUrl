<?php

namespace SfpIteratorUrl;

/**
 * implements streamWrapper
 * http://php.net/manual/en/class.streamwrapper.php.
 */
class StreamWrapper
{
    const KEY_CONTEXT = 'sfp.iterator';

    public $context;

    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * did read length.
     *
     * @var int
     */
    private $length = 0;

    /**
     * remained strings on reading.
     *
     * @var string
     */
    private $remains = '';

    /**
     * implements streamWrapper::stream_eof
     * http://php.net/manual/en/streamwrapper.stream-eof.php.
     *
     * @return bool
     */
    public function stream_eof()
    {
        return !$this->iterator->valid();
    }

    /**
     * implements streamWrapper::stream_open
     * http://php.net/manual/en/streamwrapper.stream-open.php.
     *
     * @param string $path
     * @param string $mode
     * @param int    $options
     * @param string $opened_path
     *
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        if (!preg_match('/^r[bt]?$/', $mode) || !$this->context) {
            return false;
        }

        $opt = stream_context_get_options($this->context);

        if (!is_array($opt[static::class]) ||
            !isset($opt[static::class][static::KEY_CONTEXT])) {
            return false;
        }

        $this->iterator = $opt[self::class][static::KEY_CONTEXT];

        return true;
    }

    public function stream_read($count)
    {
        $contents = $this->remains;

        while ($this->iterator->valid() &&
                ($count - strlen($contents)) >= 0) {
            $content = $this->iterator->current();
            $contents .= $content;
            $this->length += strlen($content);
            $this->iterator->next();
        }

        $this->remains = substr($contents, $count);

        return substr($contents, 0, $count);
    }

    public function stream_seek($offset, $whence = SEEK_SET)
    {
        if ($whence == SEEK_END) {
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

    /**
     * If iterator has stream_stat() method,
     * will be use it.
     *
     * @return array
     */
    public function stream_stat()
    {
        return method_exists(
            $this->iterator,
            __FUNCTION__
        ) ? $this->iterator->{__FUNCTION__}() : [
            'dev' => -1,
            'ino' => -1,
            'mode' => -1,
            'nlink' => -1,
            'uid' => -1,
            'gid' => -1,
            'rdev' => -1,
            'size' => -1,
            'atime' => -1,
            'mtime' => -1,
            'ctime' => -1,
            'blksize' => -1,
            'blocks' => -1,
        ];
    }

    /**
     * @return int
     */
    public function stream_tell()
    {
        return $this->length;
    }
}
