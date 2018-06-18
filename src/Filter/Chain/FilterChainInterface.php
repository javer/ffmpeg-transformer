<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use Javer\FfmpegTransformer\Filter\Graph\FilterGraphInterface;
use Javer\FfmpegTransformer\Stream\StreamInterface;

/**
 * Interface FilterChainInterface
 *
 * @package Javer\FfmpegTransformer\Filter\Chain
 */
interface FilterChainInterface
{
    /**
     * Add filter.
     *
     * @param string $name
     * @param array  $arguments
     * @param array  $inputs
     * @param array  $outputs
     *
     * @return FilterChainInterface
     */
    public function filter(string $name, array $arguments, array $inputs, array $outputs): FilterChainInterface;

    /**
     * Add input stream.
     *
     * @param StreamInterface $stream
     *
     * @return FilterChainInterface
     */
    public function addInputStream(StreamInterface $stream): FilterChainInterface;

    /**
     * Get output stream.
     *
     * @param integer $number
     *
     * @return StreamInterface
     */
    public function getOutputStream(int $number = 0): StreamInterface;

    /**
     * Returns output streams.
     *
     * @param string $type
     *
     * @return StreamInterface[]
     */
    public function getOutputStreams(string $type = ''): array;

    /**
     * Build chain.
     *
     * @return string
     */
    public function build(): string;

    /**
     * Returns a string representation of the chain.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Return to FilterGraph.
     *
     * @return FilterGraphInterface
     */
    public function end(): FilterGraphInterface;
}
