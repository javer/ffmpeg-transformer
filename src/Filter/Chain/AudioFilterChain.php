<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use InvalidArgumentException;

/**
 * Class AudioFilterChain
 *
 * @package Javer\FfmpegTransformer\Filter\Chain
 */
class AudioFilterChain extends FilterChain implements AudioFilterChainInterface
{
    /**
     * Channel split filter.
     *
     * @param array<string|int, mixed> $arguments
     *
     * @return static
     *
     * @throws InvalidArgumentException
     */
    public function channelsplit(array $arguments = []): static
    {
        if (!isset($arguments['channel_layout'])) {
            throw new InvalidArgumentException('You must specify channel_layout for the channelsplit filter');
        }

        $channels = match ($arguments['channel_layout']) {
            'mono' => 1,
            'stereo' => 2,
            '2.1' => 3,
            '5.1' => 6,
            default => (int) $arguments['channel_layout'],
        };

        return $this->filter('channelsplit', $arguments, ['a'], array_fill(0, $channels, 'a'));
    }

    /**
     * Amplify volume level.
     *
     * @param float $volume
     *
     * @return static
     */
    public function volume(float $volume): static
    {
        return $this->filter('volume', [sprintf('%fdB', $volume)], ['a'], ['a']);
    }

    /**
     * Split filter.
     *
     * @param integer $count
     *
     * @return static
     */
    public function split(int $count): static
    {
        return $this->filter('asplit', [$count], ['a'], array_fill(0, $count, 'a'));
    }

    /**
     * Mix filter.
     *
     * @param array<string|int, mixed> $arguments
     *
     * @return static
     */
    public function mix(array $arguments = []): static
    {
        $arguments['inputs'] ??= count($this->outputs);

        return $this->filter('amix', $arguments, array_fill(0, $arguments['inputs'], 'a'), ['a']);
    }

    /**
     * Trim filter.
     *
     * @param float $start
     * @param float $end
     *
     * @return static
     */
    public function trim(float $start, float $end): static
    {
        return $this->filter('atrim', [$start, $end], ['a'], ['a']);
    }

    /**
     * Set PTS filter.
     *
     * @param string $expr
     *
     * @return static
     */
    public function setpts(string $expr): static
    {
        return $this->filter('asetpts', [$expr], ['a'], ['a']);
    }

    /**
     * Reset stream timestamps.
     *
     * @return static
     */
    public function resetTimestamp(): static
    {
        return $this->setpts('PTS-STARTPTS');
    }

    /**
     * Fade filter.
     *
     * @param string $type
     * @param float  $start
     * @param float  $end
     *
     * @return static
     */
    public function fade(string $type, float $start, float $end): static
    {
        $arguments = [
            'enable' => sprintf("'between(t,%f,%f)'", $start, $end),
            't' => $type,
            'st' => $start,
        ];

        return $this->filter('afade', $arguments, ['a'], ['a']);
    }

    /**
     * Dynamic Audio Normalizer filter.
     *
     * @param array<string|int, mixed> $arguments
     *
     * @return static
     */
    public function dynaudnorm(array $arguments = []): static
    {
        return $this->filter('dynaudnorm', $arguments, ['a'], ['a']);
    }
}
