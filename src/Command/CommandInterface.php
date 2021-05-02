<?php

namespace Javer\FfmpegTransformer\Command;

use Javer\FfmpegTransformer\BuilderInterface;
use Javer\FfmpegTransformer\File\FileInterface;

/**
 * Interface CommandInterface
 *
 * @package Javer\FfmpegTransformer\Command
 */
interface CommandInterface extends BuilderInterface
{
    /**
     * Log level.
     *
     * Option: -v loglevel
     *
     * @param string $logLevel
     *
     * @return static
     */
    public function logLevel(string $logLevel): static;

    /**
     * Overwrite output files.
     *
     * Option: -y || -n
     *
     * @param boolean $flag
     *
     * @return static
     */
    public function overwriteOutputFiles(bool $flag = true): static;

    /**
     * Ignore unknown stream types.
     *
     * Option: -ignore_unknown
     *
     * @return static
     */
    public function ignoreUnknownStreamTypes(): static;

    /**
     * Print progress report.
     *
     * Option: -stats
     *
     * @return static
     */
    public function printProgressReport(): static;

    /**
     * Max error rate.
     *
     * Option: -max_error_rate ratio
     *
     * @param float $ratio
     *
     * @return static
     */
    public function maxErrorRate(float $ratio): static;

    /**
     * Bits per raw sample.
     *
     * Option: -bits_per_raw_sample number
     *
     * @param integer $number
     *
     * @return static
     */
    public function bitsPerRawSample(int $number): static;

    /**
     * Volume.
     *
     * Option: -vol volume
     *
     * @param integer $volume
     *
     * @return static
     */
    public function volume(int $volume): static;

    /**
     * Add input file.
     *
     * @param string $filename
     *
     * @return FileInterface
     */
    public function addInput(string $filename): FileInterface;

    /**
     * Add output file.
     *
     * @param string $filename
     *
     * @return FileInterface
     */
    public function addOutput(string $filename): FileInterface;

    /**
     * Generate black video frames.
     *
     * @param integer $width
     * @param integer $height
     * @param float   $duration
     *
     * @return FileInterface
     */
    public function generateBlackVideo(int $width, int $height, float $duration): FileInterface;

    /**
     * Generate empty audio.
     *
     * @param float $duration
     *
     * @return FileInterface
     */
    public function generateEmptyAudio(float $duration): FileInterface;

    /**
     * Generate video from static picture.
     *
     * @param string $filename
     * @param float  $duration
     *
     * @return FileInterface
     */
    public function generateVideoFromPicture(string $filename, float $duration): FileInterface;

    /**
     * Add an option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return static
     */
    public function addOption(string $name, string $argument = ''): static;
}
