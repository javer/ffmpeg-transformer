<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use Javer\FfmpegTransformer\Stream\AudioStreamInterface;
use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

/**
 * Interface ComplexFilterChainInterface
 *
 * @package Javer\FfmpegTransformer\Filter\Chain
 */
interface ComplexFilterChainInterface extends FilterChainInterface
{
    /**
     * Concat filter.
     *
     * Example: concat=n=%d:v=1:a=%d
     *
     * @return ComplexFilterChainInterface
     */
    public function concat(): ComplexFilterChainInterface;

    /**
     * Get output video stream by number.
     *
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function getOutputVideoStream(int $number = 0): VideoStreamInterface;

    /**
     * Get output video streams.
     *
     * @return VideoStreamInterface[]
     */
    public function getOutputVideoStreams(): array;

    /**
     * Get output audio stream by number.
     *
     * @param integer $number
     *
     * @return AudioStreamInterface
     */
    public function getOutputAudioStream(int $number = 0): AudioStreamInterface;

    /**
     * Get output audio streams.
     *
     * @return AudioStreamInterface[]
     */
    public function getOutputAudioStreams(): array;
}
