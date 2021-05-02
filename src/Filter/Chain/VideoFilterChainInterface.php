<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

/**
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
     * @param int                      $width
     * @param int                      $height
     * @param array<string|int, mixed> $arguments
     *
     * @return static
     */
    public function scale(int $width, int $height, array $arguments = []): static;

    /**
     * Setsar filter.
     *
     * @param int $num
     * @param int $den
     *
     * @return static
     */
    public function setsar(int $num, int $den): static;
}
