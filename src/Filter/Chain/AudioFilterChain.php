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
     * @param array $arguments
     *
     * @return AudioFilterChainInterface
     *
     * @throws InvalidArgumentException
     */
    public function channelsplit(array $arguments = []): AudioFilterChainInterface
    {
        if (!isset($arguments['channel_layout'])) {
            throw new InvalidArgumentException('You must specify channel_layout for the channelsplit filter');
        }

        switch ($arguments['channel_layout']) {
            case 'mono':
                $channels = 1;
                break;
            case 'stereo':
                $channels = 2;
                break;
            case '2.1':
                $channels = 3;
                break;
            case '5.1':
                $channels = 6;
                break;
            default:
                $channels = (int) $arguments['channel_layout'];
        }

        return $this->filter('channelsplit', $arguments, ['a'], array_fill(0, $channels, 'a'));
    }

    /**
     * Amplify volume level.
     *
     * @param float $volume
     *
     * @return AudioFilterChainInterface
     */
    public function volume(float $volume): AudioFilterChainInterface
    {
        return $this->filter('volume', [sprintf('%fdB', $volume)], ['a'], ['a']);
    }

    /**
     * Split filter.
     *
     * @param integer $count
     *
     * @return AudioFilterChainInterface
     */
    public function split(int $count): AudioFilterChainInterface
    {
        return $this->filter('asplit', [$count], ['a'], array_fill(0, $count, 'a'));
    }

    /**
     * Mix filter.
     *
     * @param array $arguments
     *
     * @return AudioFilterChainInterface
     */
    public function mix(array $arguments = []): AudioFilterChainInterface
    {
        if (!isset($arguments['inputs'])) {
            $arguments['inputs'] = count($this->outputs);
        }

        return $this->filter('amix', $arguments, array_fill(0, $arguments['inputs'], 'a'), ['a']);
    }

    /**
     * Trim filter.
     *
     * @param float $start
     * @param float $end
     *
     * @return AudioFilterChainInterface
     */
    public function trim(float $start, float $end): AudioFilterChainInterface
    {
        return $this->filter('atrim', [$start, $end], ['a'], ['a']);
    }

    /**
     * Set PTS filter.
     *
     * @param string $expr
     *
     * @return AudioFilterChainInterface
     */
    public function setpts(string $expr): AudioFilterChainInterface
    {
        return $this->filter('asetpts', [$expr], ['a'], ['a']);
    }

    /**
     * Reset stream timestamps.
     *
     * @return AudioFilterChainInterface
     */
    public function resetTimestamp(): AudioFilterChainInterface
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
     * @return AudioFilterChainInterface
     */
    public function fade(string $type, float $start, float $end): AudioFilterChainInterface
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
     * @param array $arguments
     *
     * @return AudioFilterChainInterface
     */
    public function dynaudnorm(array $arguments = []): AudioFilterChainInterface
    {
        return $this->filter('dynaudnorm', $arguments, ['a'], ['a']);
    }
}
