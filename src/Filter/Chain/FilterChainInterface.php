<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use Javer\FfmpegTransformer\Filter\Graph\FilterGraphInterface;
use Javer\FfmpegTransformer\Stream\StreamInterface;

interface FilterChainInterface
{
    /**
     * Add filter.
     *
     * @param string                   $name
     * @param array<string|int, mixed> $arguments
     * @param array<int, string>       $inputs
     * @param array<int, string>       $outputs
     *
     * @return static
     */
    public function filter(string $name, array $arguments, array $inputs, array $outputs): static;

    /**
     * Add input stream.
     *
     * @param StreamInterface $stream
     *
     * @return static
     */
    public function addInputStream(StreamInterface $stream): static;

    /**
     * Get output stream.
     *
     * @param int $number
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
