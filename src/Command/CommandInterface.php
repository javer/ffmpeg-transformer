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
     * @return CommandInterface
     */
    public function logLevel(string $logLevel): CommandInterface;

    /**
     * Overwrite output files.
     *
     * Option: -y || -n
     *
     * @param boolean $flag
     *
     * @return CommandInterface
     */
    public function overwriteOutputFiles(bool $flag = true): CommandInterface;

    /**
     * Ignore unknown stream types.
     *
     * Option: -ignore_unknown
     *
     * @return CommandInterface
     */
    public function ignoreUnknownStreamTypes(): CommandInterface;

    /**
     * Print progress report.
     *
     * Option: -stats
     *
     * @return CommandInterface
     */
    public function printProgressReport(): CommandInterface;

    /**
     * Max error rate.
     *
     * Option: -max_error_rate ratio
     *
     * @param float $ratio
     *
     * @return CommandInterface
     */
    public function maxErrorRate(float $ratio): CommandInterface;

    /**
     * Bits per raw sample.
     *
     * Option: -bits_per_raw_sample number
     *
     * @param integer $number
     *
     * @return CommandInterface
     */
    public function bitsPerRawSample(int $number): CommandInterface;

    /**
     * Volume.
     *
     * Option: -vol volume
     *
     * @param integer $volume
     *
     * @return CommandInterface
     */
    public function volume(int $volume): CommandInterface;

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
     * @return CommandInterface
     */
    public function addOption(string $name, string $argument = ''): CommandInterface;
}
