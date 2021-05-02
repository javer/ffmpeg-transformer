<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use Javer\FfmpegTransformer\Stream\AudioStreamInterface;
use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

interface ComplexFilterChainInterface extends FilterChainInterface
{
    /**
     * Concat filter.
     *
     * Example: concat=n=%d:v=1:a=%d
     *
     * @return static
     */
    public function concat(): static;

    /**
     * Get output video stream by number.
     *
     * @param int $number
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
     * @param int $number
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
