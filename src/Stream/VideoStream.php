<?php

namespace Javer\FfmpegTransformer\Stream;

use Javer\FfmpegTransformer\File\FileInterface;

/**
 * Class VideoStream
 *
 * @package Javer\FfmpegTransformer\Stream
 */
class VideoStream extends Stream implements VideoStreamInterface
{
    /**
     * VideoStream constructor.
     *
     * @param FileInterface $file
     * @param string|null   $name
     * @param string        $type
     * @param boolean       $isInput
     * @param boolean       $isMapped
     */
    public function __construct(
        FileInterface $file,
        $name = null,
        string $type = '',
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
     * @return VideoStreamInterface
     */
    public function addOption(string $name, string $argument = ''): VideoStreamInterface
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
     * @return VideoStreamInterface
     */
    public function addStreamOption(string $name, string $argument = ''): VideoStreamInterface
    {
        $this->options[] = [$name, $argument, true];

        return $this;
    }

    /**
     * Copy stream "as is".
     *
     * @return VideoStreamInterface
     */
    public function copy(): VideoStreamInterface
    {
        $this->isCustomCodec = false;

        return $this;
    }

    /**
     * Disable video stream.
     *
     * @return VideoStreamInterface
     */
    public function disable(): VideoStreamInterface
    {
        $this->isCustomCodec = true;

        return $this->addOption('-vn');
    }

    /**
     * Map video stream.
     *
     * @param VideoStreamInterface $stream
     *
     * @return VideoStreamInterface
     */
    public function map(VideoStreamInterface $stream): VideoStreamInterface
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
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function frames(int $number): VideoStreamInterface
    {
        return $this->addStreamOption('-frames', $number);
    }

    /**
     * Set frame rate.
     *
     * @param float $rate
     *
     * @return VideoStreamInterface
     */
    public function frameRate(float $rate): VideoStreamInterface
    {
        return $this->addStreamOption('-r', $rate);
    }

    /**
     * Set frame size.
     *
     * @param string $size
     *
     * @return VideoStreamInterface
     */
    public function frameSize(string $size): VideoStreamInterface
    {
        return $this->addStreamOption('-s', $size);
    }

    /**
     * Set aspect ratio.
     *
     * @param string $aspect
     *
     * @return VideoStreamInterface
     */
    public function aspectRatio(string $aspect): VideoStreamInterface
    {
        return $this->addStreamOption('-aspect', $aspect);
    }

    /**
     * Set bits per raw sample.
     *
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function bitsPerRawSample(int $number): VideoStreamInterface
    {
        return $this->addOption('-bits_per_raw_sample', $number);
    }

    /**
     * Set codec.
     *
     * @param string $codec
     *
     * @return VideoStreamInterface
     */
    public function codec(string $codec): VideoStreamInterface
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
     * @return VideoStreamInterface
     */
    public function profile(string $profile): VideoStreamInterface
    {
        return $this->addStreamOption('-profile', $profile);
    }

    /**
     * Set preset.
     *
     * @param string $preset
     *
     * @return VideoStreamInterface
     */
    public function preset(string $preset): VideoStreamInterface
    {
        return $this->addOption('-preset', $preset);
    }

    /**
     * Set time code.
     *
     * @param string $timecode
     *
     * @return VideoStreamInterface
     */
    public function timeCode(string $timecode): VideoStreamInterface
    {
        return $this->addOption('-timecode', $timecode);
    }

    /**
     * Set pass.
     *
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function pass(int $number): VideoStreamInterface
    {
        return $this->addStreamOption('-pass', $number);
    }

    /**
     * Set filter graph as a string.
     *
     * @param string $filterGraph
     *
     * @return VideoStreamInterface
     */
    public function filter(string $filterGraph): VideoStreamInterface
    {
        return $this->addOption('-vf', $filterGraph);
    }

    /**
     * Set bitrate.
     *
     * @param string $bitrate
     *
     * @return VideoStreamInterface
     */
    public function bitrate(string $bitrate): VideoStreamInterface
    {
        return $this->addStreamOption('-b', $bitrate);
    }

    /**
     * Set max bitrate.
     *
     * @param string $maxBitrate
     *
     * @return VideoStreamInterface
     */
    public function maxBitrate(string $maxBitrate): VideoStreamInterface
    {
        return $this->addStreamOption('-maxrate', $maxBitrate);
    }

    /**
     * Set min bitrate.
     *
     * @param string $minBitrate
     *
     * @return VideoStreamInterface
     */
    public function minBitrate(string $minBitrate): VideoStreamInterface
    {
        return $this->addStreamOption('-minrate', $minBitrate);
    }

    /**
     * Set buffer size.
     *
     * @param string $bufferSize
     *
     * @return VideoStreamInterface
     */
    public function bufferSize(string $bufferSize): VideoStreamInterface
    {
        return $this->addStreamOption('-bufsize', $bufferSize);
    }

    /**
     * Set crf.
     *
     * @param integer $crf
     *
     * @return VideoStreamInterface
     */
    public function crf(int $crf): VideoStreamInterface
    {
        return $this->addStreamOption('-crf', $crf);
    }

    /**
     * Set pixel format.
     *
     * @param string $format
     *
     * @return VideoStreamInterface
     */
    public function pixelFormat(string $format): VideoStreamInterface
    {
        return $this->addStreamOption('-pix_fmt', $format);
    }

    /**
     * Set keyframe interval.
     *
     * @param integer $interval
     *
     * @return VideoStreamInterface
     */
    public function keyframeInterval(int $interval): VideoStreamInterface
    {
        return $this->addOption('-g', $interval);
    }
}
