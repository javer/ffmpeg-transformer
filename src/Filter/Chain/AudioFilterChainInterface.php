<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use Javer\FfmpegTransformer\Stream\AudioStreamInterface;

/**
 * @method AudioStreamInterface getOutputStream(int $number = 0)
 */
interface AudioFilterChainInterface extends FilterChainInterface
{
    /**
     * Channel split filter.
     *
     * Example: channelsplit=channel_layout=%dc
     *
     * @param array<string|int, mixed> $arguments
     *
     * @return static
     */
    public function channelsplit(array $arguments = []): static;

    /**
     * Amplify volume level.
     *
     * Example: volume=%0.1fdB
     *
     * @param float $volume
     *
     * @return static
     */
    public function volume(float $volume): static;

    /**
     * Split filter.
     *
     * Example: asplit
     *
     * @param int $count
     *
     * @return static
     */
    public function split(int $count): static;

    /**
     * Mix filter.
     *
     * Example: amix=inputs=%2$d
     *
     * @param array<string|int, mixed> $arguments
     *
     * @return static
     */
    public function mix(array $arguments = []): static;

    /**
     * Trim filter.
     *
     * Example: atrim=%0.6f:%0.6f
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
     * Example: asetpts=PTS-STARTPTS
     *
     * @param string $expr
     *
     * @return static
     */
    public function setpts(string $expr): static;

    /**
     * Reset stream timestamps.
     *
     * Example: asetpts=PTS-STARTPTS
     *
     * @return static
     */
    public function resetTimestamp(): static;

    /**
     * Fade filter.
     *
     * Example: afade=enable='between(t,%d,%d)':t=out:st=%d
     *
     * @param string $type
     * @param float  $start
     * @param float  $end
     *
     * @return static
     */
    public function fade(string $type, float $start, float $end): static;

    /**
     * Dynamic Audio Normalizer filter.
     *
     * Example: dynaudnorm=g=3
     *
     * @param array<string|int, mixed> $arguments
     *
     * @return static
     */
    public function dynaudnorm(array $arguments = []): static;
}
