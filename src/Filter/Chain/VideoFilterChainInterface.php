<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

/**
 * Interface VideoFilterChainInterface
 *
 * @package Javer\FfmpegTransformer\Filter\Chain
 */
interface VideoFilterChainInterface extends FilterChainInterface
{
    /**
     * Trim filter.
     *
     * Example: trim=%0.6f:%0.6f
     *
     * @param float $start
     * @param float $end
     *
     * @return VideoFilterChainInterface
     */
    public function trim(float $start, float $end): VideoFilterChainInterface;

    /**
     * Set PTS filter.
     *
     * Example: setpts=PTS-STARTPTS
     *
     * @param string $expr
     *
     * @return VideoFilterChainInterface
     */
    public function setpts(string $expr): VideoFilterChainInterface;

    /**
     * Reset stream timestamps.
     *
     * Example: setpts=PTS-STARTPTS
     *
     * @return VideoFilterChainInterface
     */
    public function resetTimestamp(): VideoFilterChainInterface;

    /**
     * Scale filter.
     *
     * @param integer $width
     * @param integer $height
     * @param array   $arguments
     *
     * @return VideoFilterChainInterface
     */
    public function scale(int $width, int $height, array $arguments = []): VideoFilterChainInterface;

    /**
     * Setsar filter.
     *
     * @param integer $num
     * @param integer $den
     *
     * @return VideoFilterChainInterface
     */
    public function setsar(int $num, int $den): VideoFilterChainInterface;
}
