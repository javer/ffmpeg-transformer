<?php

namespace Javer\FfmpegTransformer;

/**
 * Interface BuilderInterface
 *
 * @package Javer\FfmpegTransformer
 */
interface BuilderInterface
{
    /**
     * Build command.
     *
     * @return array<int, string>
     */
    public function build(): array;

    /**
     * Returns a string representation of the command.
     *
     * @return string
     */
    public function __toString(): string;
}
