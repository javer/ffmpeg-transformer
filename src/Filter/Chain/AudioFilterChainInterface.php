<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

/**
 * Interface AudioFilterChainInterface
 *
 * @package Javer\FfmpegTransformer\Filter\Chain
 */
interface AudioFilterChainInterface extends FilterChainInterface
{
    /**
     * Channel split filter.
     *
     * Example: channelsplit=channel_layout=%dc
     *
     * @param array $arguments
     *
     * @return AudioFilterChainInterface
     */
    public function channelsplit(array $arguments = []): AudioFilterChainInterface;

    /**
     * Amplify volume level.
     *
     * Example: volume=%0.1fdB
     *
     * @param float $volume
     *
     * @return AudioFilterChainInterface
     */
    public function volume(float $volume): AudioFilterChainInterface;

    /**
     * Split filter.
     *
     * Example: asplit
     *
     * @param integer $count
     *
     * @return AudioFilterChainInterface
     */
    public function split(int $count): AudioFilterChainInterface;

    /**
     * Mix filter.
     *
     * Example: amix=inputs=%2$d
     *
     * @param array $arguments
     *
     * @return AudioFilterChainInterface
     */
    public function mix(array $arguments = []): AudioFilterChainInterface;

    /**
     * Trim filter.
     *
     * Example: atrim=%0.6f:%0.6f
     *
     * @param float $start
     * @param float $end
     *
     * @return AudioFilterChainInterface
     */
    public function trim(float $start, float $end): AudioFilterChainInterface;

    /**
     * Set PTS filter.
     *
     * Example: asetpts=PTS-STARTPTS
     *
     * @param string $expr
     *
     * @return AudioFilterChainInterface
     */
    public function setpts(string $expr): AudioFilterChainInterface;

    /**
     * Reset stream timestamps.
     *
     * Example: asetpts=PTS-STARTPTS
     *
     * @return AudioFilterChainInterface
     */
    public function resetTimestamp(): AudioFilterChainInterface;

    /**
     * Fade filter.
     *
     * Example: afade=enable='between(t,%d,%d)':t=out:st=%d
     *
     * @param string $type
     * @param float  $start
     * @param float  $end
     *
     * @return AudioFilterChainInterface
     */
    public function fade(string $type, float $start, float $end): AudioFilterChainInterface;

    /**
     * Dynamic Audio Normalizer filter.
     *
     * Example: dynaudnorm=g=3
     *
     * @param array $arguments
     *
     * @return AudioFilterChainInterface
     */
    public function dynaudnorm(array $arguments = []): AudioFilterChainInterface;
}
