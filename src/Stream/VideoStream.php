<?php

namespace Javer\FfmpegTransformer\Stream;

use Javer\FfmpegTransformer\File\FileInterface;

class VideoStream extends Stream implements VideoStreamInterface
{
    /**
     * VideoStream constructor.
     *
     * @param FileInterface   $file
     * @param string|int|null $name
     * @param bool            $isInput
     * @param bool            $isMapped
     */
    public function __construct(
        FileInterface $file,
        string|int|null $name = null,
        bool $isInput = false,
        bool $isMapped = true
    )
    {
        parent::__construct($file, $name, StreamInterface::TYPE_VIDEO, $isInput, $isMapped);
    }

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
        $this->options[] = [$name, $argument, false];

        return $this;
    }

    /**
     * Add per-stream option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return static
     */
    public function addStreamOption(string $name, string $argument = ''): static
    {
        $this->options[] = [$name, $argument, true];

        return $this;
    }

    /**
     * Copy stream "as is".
     *
     * @return static
     */
    public function copy(): static
    {
        $this->isCustomCodec = false;

        return $this;
    }

    /**
     * Disable video stream.
     *
     * @return static
     */
    public function disable(): static
    {
        $this->isCustomCodec = true;

        return $this->addOption('-vn');
    }

    /**
     * Map video stream.
     *
     * @param VideoStreamInterface $stream
     *
     * @return static
     */
    public function map(VideoStreamInterface $stream): static
    {
        $this->file->removeStream($stream);

        if (!$stream->getInput()) {
            $this->name = $stream->getName();
        }

        return $this->addOption('-map', sprintf($stream->getInput() ? '%s' : '[%s]', $stream->getName()));
    }

    /**
     * Set frames number.
     *
     * @param int $number
     *
     * @return static
     */
    public function frames(int $number): static
    {
        return $this->addStreamOption('-frames', (string) $number);
    }

    /**
     * Set frame rate.
     *
     * @param float $rate
     *
     * @return static
     */
    public function frameRate(float $rate): static
    {
        return $this->addStreamOption('-r', (string) $rate);
    }

    /**
     * Set frame size.
     *
     * @param string $size
     *
     * @return static
     */
    public function frameSize(string $size): static
    {
        return $this->addStreamOption('-s', $size);
    }

    /**
     * Set aspect ratio.
     *
     * @param string $aspect
     *
     * @return static
     */
    public function aspectRatio(string $aspect): static
    {
        return $this->addStreamOption('-aspect', $aspect);
    }

    /**
     * Set bits per raw sample.
     *
     * @param int $number
     *
     * @return static
     */
    public function bitsPerRawSample(int $number): static
    {
        return $this->addOption('-bits_per_raw_sample', (string) $number);
    }

    /**
     * Set codec.
     *
     * @param string $codec
     *
     * @return static
     */
    public function codec(string $codec): static
    {
        $this->addStreamOption('-c', $codec);
        $this->isCustomCodec = true;

        return $this;
    }

    /**
     * Set profile.
     *
     * @param string $profile
     *
     * @return static
     */
    public function profile(string $profile): static
    {
        return $this->addStreamOption('-profile', $profile);
    }

    /**
     * Set preset.
     *
     * @param string $preset
     *
     * @return static
     */
    public function preset(string $preset): static
    {
        return $this->addOption('-preset', $preset);
    }

    /**
     * Set time code.
     *
     * @param string $timecode
     *
     * @return static
     */
    public function timeCode(string $timecode): static
    {
        return $this->addOption('-timecode', $timecode);
    }

    /**
     * Set pass.
     *
     * @param int $number
     *
     * @return static
     */
    public function pass(int $number): static
    {
        return $this->addStreamOption('-pass', (string) $number);
    }

    /**
     * Set filter graph as a string.
     *
     * @param string $filterGraph
     *
     * @return static
     */
    public function filter(string $filterGraph): static
    {
        return $this->addOption('-vf', $filterGraph);
    }

    /**
     * Set bitrate.
     *
     * @param string $bitrate
     *
     * @return static
     */
    public function bitrate(string $bitrate): static
    {
        return $this->addStreamOption('-b', $bitrate);
    }

    /**
     * Set max bitrate.
     *
     * @param string $maxBitrate
     *
     * @return static
     */
    public function maxBitrate(string $maxBitrate): static
    {
        return $this->addStreamOption('-maxrate', $maxBitrate);
    }

    /**
     * Set min bitrate.
     *
     * @param string $minBitrate
     *
     * @return static
     */
    public function minBitrate(string $minBitrate): static
    {
        return $this->addStreamOption('-minrate', $minBitrate);
    }

    /**
     * Set buffer size.
     *
     * @param string $bufferSize
     *
     * @return static
     */
    public function bufferSize(string $bufferSize): static
    {
        return $this->addStreamOption('-bufsize', $bufferSize);
    }

    /**
     * Set crf.
     *
     * @param int $crf
     *
     * @return static
     */
    public function crf(int $crf): static
    {
        return $this->addStreamOption('-crf', (string) $crf);
    }

    /**
     * Set pixel format.
     *
     * @param string $format
     *
     * @return static
     */
    public function pixelFormat(string $format): static
    {
        return $this->addStreamOption('-pix_fmt', $format);
    }

    /**
     * Set keyframe interval.
     *
     * @param int $interval
     *
     * @return static
     */
    public function keyframeInterval(int $interval): static
    {
        return $this->addOption('-g', (string) $interval);
    }
}
