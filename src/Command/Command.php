<?php

namespace Javer\FfmpegTransformer\Command;

use Javer\FfmpegTransformer\File\File;
use Javer\FfmpegTransformer\File\FileInterface;
use LogicException;

/**
 * Class Command
 *
 * @package Javer\FfmpegTransformer\Command
 */
class Command implements CommandInterface
{
    /**
     * @var string[]
     */
    protected array $options = [];

    /**
     * @var FileInterface[]
     */
    protected array $inputFiles = [];

    /**
     * @var FileInterface[]
     */
    protected array $outputFiles = [];

    /**
     * Add an option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return static
     */
    public function addOption(string $name, string $argument = ''): static
    {
        $this->options[] = $name;

        if ($argument) {
            $this->options[] = $argument;
        }

        return $this;
    }

    /**
     * Build command.
     *
     * @return array<int, string>
     *
     * @throws LogicException
     */
    public function build(): array
    {
        $options = $this->options;

        if (count($this->inputFiles) === 0) {
            throw new LogicException('You should specify at least one input file');
        }

        if (count($this->outputFiles) === 0) {
            throw new LogicException('You should specify at least one output file');
        }

        foreach ($this->inputFiles as $inputFile) {
            $options = array_merge($options, $inputFile->build());
        }

        foreach ($this->outputFiles as $outputFile) {
            $options = array_merge($options, $outputFile->build());
        }

        return $options;
    }

    /**
     * Returns a string representation of the command.
     *
     * @return string
     */
    public function __toString(): string
    {
        return implode(' ', array_map('escapeshellarg', $this->build()));
    }

    /**
     * Clones the current command.
     */
    public function __clone()
    {
        $this->options = [];
        $this->inputFiles = [];
        $this->outputFiles = [];
    }

    /**
     * Log level.
     *
     * @param string $logLevel
     *
     * @return static
     */
    public function logLevel(string $logLevel): static
    {
        return $this->addOption('-v', $logLevel);
    }

    /**
     * Overwrite output files.
     *
     * @param boolean $flag
     *
     * @return static
     */
    public function overwriteOutputFiles(bool $flag = true): static
    {
        if (($optIndex = array_search($flag ? '-n' : '-y', $this->options, true)) !== false) {
            unset($this->options[$optIndex]);
        }

        return $this->addOption($flag ? '-y' : '-n');
    }

    /**
     * Ignore unknown stream types.
     *
     * @return static
     */
    public function ignoreUnknownStreamTypes(): static
    {
        return $this->addOption('-ignore_unknown');
    }

    /**
     * Print progress report.
     *
     * @return static
     */
    public function printProgressReport(): static
    {
        return $this->addOption('-stats');
    }

    /**
     * Max error rate.
     *
     * @param float $ratio
     *
     * @return static
     */
    public function maxErrorRate(float $ratio): static
    {
        return $this->addOption('-max_error_rate', (string) $ratio);
    }

    /**
     * Bits per raw sample.
     *
     * @param integer $number
     *
     * @return static
     */
    public function bitsPerRawSample(int $number): static
    {
        return $this->addOption('-bits_per_raw_sample', (string) $number);
    }

    /**
     * Volume.
     *
     * @param integer $volume
     *
     * @return static
     */
    public function volume(int $volume): static
    {
        return $this->addOption('-vol', (string) $volume);
    }

    /**
     * Add input file.
     *
     * @param string $filename
     *
     * @return FileInterface
     */
    public function addInput(string $filename): FileInterface
    {
        $file = new File($this, $filename, (string) count($this->inputFiles), true);

        $this->inputFiles[] = $file;

        return $file;
    }

    /**
     * Add output file.
     *
     * @param string $filename
     *
     * @return FileInterface
     */
    public function addOutput(string $filename): FileInterface
    {
        $file = new File($this, $filename);

        $this->outputFiles[] = $file;

        return $file;
    }

    /**
     * Generate black video frames.
     *
     * @param integer $width
     * @param integer $height
     * @param float   $duration
     *
     * @return FileInterface
     */
    public function generateBlackVideo(int $width, int $height, float $duration): FileInterface
    {
        return $this->addInput('/dev/zero')
            ->format('rawvideo')
            ->duration($duration)
            ->getVideoStream()
                ->frameSize(sprintf('%dx%d', $width, $height))
                ->pixelFormat('rgb24')
            ->end();
    }

    /**
     * Generate empty audio.
     *
     * @param float $duration
     *
     * @return FileInterface
     */
    public function generateEmptyAudio(float $duration): FileInterface
    {
        return $this->addInput('aevalsrc=0')
            ->format('lavfi')
            ->duration($duration);
    }

    /**
     * Generate video from static picture.
     *
     * @param string $filename
     * @param float  $duration
     *
     * @return FileInterface
     */
    public function generateVideoFromPicture(string $filename, float $duration): FileInterface
    {
        return $this->addInput($filename)
            ->loop()
            ->format('image2')
            ->duration($duration);
    }
}
