<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

class VideoFilterChain extends FilterChain implements VideoFilterChainInterface
{
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
        return $this->filter('trim', [$start, $end], ['v'], ['v']);
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
        return $this->filter('setpts', [$expr], ['v'], ['v']);
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
     * Scale filter.
     *
     * @param int                      $width
     * @param int                      $height
     * @param array<string|int, mixed> $arguments
     *
     * @return static
     */
    public function scale(int $width, int $height, array $arguments = []): static
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
     * @param int $num
     * @param int $den
     *
     * @return static
     */
    public function setsar(int $num, int $den): static
    {
        return $this->filter('setsar', [sprintf('%d/%d', $num, $den)], ['v'], ['v']);
    }
}
