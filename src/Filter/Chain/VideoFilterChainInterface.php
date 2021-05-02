<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

/**
 * Interface VideoFilterChainInterface
 *
 * @package Javer\FfmpegTransformer\Filter\Chain
 *
 * @method VideoStreamInterface getOutputStream(int $number = 0)
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
     * @return static
     */
    public function trim(float $start, float $end): static;

    /**
     * Set PTS filter.
     *
     * Example: setpts=PTS-STARTPTS
     *
     * @param string $expr
     *
     * @return static
     */
    public function setpts(string $expr): static;

    /**
     * Reset stream timestamps.
     *
     * Example: setpts=PTS-STARTPTS
     *
     * @return static
     */
    public function resetTimestamp(): static;

    /**
     * Scale filter.
     *
     * @param integer                  $width
     * @param integer                  $height
     * @param array<string|int, mixed> $arguments
     *
     * @return static
     */
    public function scale(int $width, int $height, array $arguments = []): static;

    /**
     * Setsar filter.
     *
     * @param integer $num
     * @param integer $den
     *
     * @return static
     */
    public function setsar(int $num, int $den): static;
}
