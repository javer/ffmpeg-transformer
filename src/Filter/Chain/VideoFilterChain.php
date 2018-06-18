<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

/**
 * Class VideoFilterChain
 *
 * @package Javer\FfmpegTransformer\Filter\Chain
 */
class VideoFilterChain extends FilterChain implements VideoFilterChainInterface
{
    /**
     * Trim filter.
     *
     * @param float $start
     * @param float $end
     *
     * @return VideoFilterChainInterface
     */
    public function trim(float $start, float $end): VideoFilterChainInterface
    {
        return $this->filter('trim', [$start, $end], ['v'], ['v']);
    }

    /**
     * Set PTS filter.
     *
     * @param string $expr
     *
     * @return VideoFilterChainInterface
     */
    public function setpts(string $expr): VideoFilterChainInterface
    {
        return $this->filter('setpts', [$expr], ['v'], ['v']);
    }

    /**
     * Reset stream timestamps.
     *
     * @return VideoFilterChainInterface
     */
    public function resetTimestamp(): VideoFilterChainInterface
    {
        return $this->setpts('PTS-STARTPTS');
    }

    /**
     * Scale filter.
     *
     * @param integer $width
     * @param integer $height
     * @param array   $arguments
     *
     * @return VideoFilterChainInterface
     */
    public function scale(int $width, int $height, array $arguments = []): VideoFilterChainInterface
    {
        $arguments = array_merge([
            'w' => $width,
            'h' => $height,
        ], $arguments);

        return $this->filter('scale', $arguments, ['v'], ['v']);
    }

    /**
     * Setsar filter.
     *
     * @param integer $num
     * @param integer $den
     *
     * @return VideoFilterChainInterface
     */
    public function setsar(int $num, int $den): VideoFilterChainInterface
    {
        return $this->filter('setsar', [sprintf('%d/%d', $num, $den)], ['v'], ['v']);
    }
}
