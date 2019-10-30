<?php

namespace Javer\FfmpegTransformer\Stream;

use Javer\FfmpegTransformer\BuilderInterface;
use Javer\FfmpegTransformer\File\FileInterface;

/**
 * Interface StreamInterface
 *
 * @package Javer\FfmpegTransformer\Stream
 */
interface StreamInterface extends BuilderInterface
{
    public const TYPE_VIDEO = 'v';
    public const TYPE_AUDIO = 'a';

    /**
     * Returns stream name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns stream type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Returns whether stream is input.
     *
     * @return boolean
     */
    public function getInput(): bool;

    /**
     * Move stream to the given position (stream index) in the output file.
     *
     * @param integer $position
     *
     * @return StreamInterface
     */
    public function moveTo(int $position): StreamInterface;

    /**
     * Return to file.
     *
     * @return FileInterface
     */
    public function end(): FileInterface;
}
