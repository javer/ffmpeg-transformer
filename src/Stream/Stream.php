<?php

namespace Javer\FfmpegTransformer\Stream;

use Javer\FfmpegTransformer\File\FileInterface;
use LogicException;

/**
 * Class Stream
 *
 * @package Javer\FfmpegTransformer\Stream
 */
abstract class Stream implements StreamInterface
{
    /**
     * @var FileInterface
     */
    protected $file;

    /**
     * @var string|integer|null
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var boolean
     */
    protected $isInput;

    /**
     * @var string[]
     */
    protected $options = [];

    /**
     * @var boolean
     */
    protected $isCustomCodec = false;

    /**
     * @var boolean
     */
    protected $isMapped = false;

    /**
     * Stream constructor.
     *
     * @param FileInterface $file
     * @param string|null   $name
     * @param string        $type
     * @param boolean       $isInput
     * @param boolean       $isMapped
     */
    public function __construct(
        FileInterface $file,
        $name = null,
        string $type = '',
        bool $isInput = false,
        bool $isMapped = true
    )
    {
        $this->file = $file;
        $this->name = is_int($name) ? trim(implode(':', [$this->file->getName(), $type, $name]), ':') : $name;
        $this->type = $type;
        $this->isInput = $isInput;
        $this->isMapped = $isMapped;
    }

    /**
     * Build command.
     *
     * @return array
     *
     * @throws LogicException
     */
    public function build(): array
    {
        if (!$this->isInput && !$this->isMapped) {
            throw new LogicException(sprintf('Stream "%s" is not connected to any output', $this->getName()));
        }

        $options = [];

        $streamSpecifier = sprintf('%s:%s', $this->getType(), $this->file->getStreamNumber($this));

        foreach ($this->options as [$name, $argument, $withSpecifier]) {
            $options[] = $withSpecifier ? sprintf('%s:%s', $name, $streamSpecifier) : $name;

            if (strlen($argument) > 0) {
                $options[] = $argument;
            }
        }

        if (!$this->isInput && !$this->isCustomCodec) {
            $options[] = sprintf('-c:%s', $streamSpecifier);
            $options[] = 'copy';
        }

        return $options;
    }

    /**
     * Returns a string representation of the stream.
     *
     * @return string
     */
    public function __toString(): string
    {
        return implode(' ', array_map('escapeshellarg', $this->build()));
    }

    /**
     * Clones the current stream.
     */
    public function __clone()
    {
        $this->options = [];
    }

    /**
     * Returns stream name.
     *
     * @return string
     */
    public function getName(): string
    {
        if (is_null($this->name)) {
            $streamNumber = $this->file->getStreamNumber($this);

            $this->name = trim(implode(':', [$this->file->getName(), $this->getType(), $streamNumber]), ':');
        }

        return $this->name;
    }

    /**
     * Returns stream type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Returns whether stream is input.
     *
     * @return boolean
     */
    public function getInput(): bool
    {
        return $this->isInput;
    }

    /**
     * Move stream to the given position (stream index) in the output file.
     *
     * @param integer $position
     *
     * @return StreamInterface
     *
     * @throws LogicException
     */
    public function moveTo(int $position): StreamInterface
    {
        $this->file->moveStreamToPosition($this, $position);

        return $this;
    }

    /**
     * Return to file.
     *
     * @return FileInterface
     */
    public function end(): FileInterface
    {
        return $this->file;
    }
}
