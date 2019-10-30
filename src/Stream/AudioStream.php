<?php

namespace Javer\FfmpegTransformer\Stream;

use Javer\FfmpegTransformer\File\FileInterface;

/**
 * Class AudioStream
 *
 * @package Javer\FfmpegTransformer\Stream
 */
class AudioStream extends Stream implements AudioStreamInterface
{
    /**
     * AudioStream constructor.
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
        parent::__construct($file, $name, StreamInterface::TYPE_AUDIO, $isInput, $isMapped);
    }

    /**
     * Add an option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return AudioStreamInterface
     */
    public function addOption(string $name, string $argument = ''): AudioStreamInterface
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
     * @return AudioStreamInterface
     */
    public function addStreamOption(string $name, string $argument = ''): AudioStreamInterface
    {
        $this->options[] = [$name, $argument, true];

        return $this;
    }

    /**
     * Copy stream "as is".
     *
     * @return AudioStreamInterface
     */
    public function copy(): AudioStreamInterface
    {
        $this->isCustomCodec = false;

        return $this;
    }

    /**
     * Disable audio stream.
     *
     * @return AudioStreamInterface
     */
    public function disable(): AudioStreamInterface
    {
        $this->isCustomCodec = true;

        return $this->addOption('-an');
    }

    /**
     * Map audio stream.
     *
     * @param AudioStreamInterface $stream
     *
     * @return AudioStreamInterface
     */
    public function map(AudioStreamInterface $stream): AudioStreamInterface
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
     * @return AudioStreamInterface
     */
    public function frames(int $number): AudioStreamInterface
    {
        return $this->addStreamOption('-frames', $number);
    }

    /**
     * Set quality.
     *
     * @param integer $quality
     *
     * @return AudioStreamInterface
     */
    public function quality(int $quality): AudioStreamInterface
    {
        return $this->addStreamOption('-q', $quality);
    }

    /**
     * Set rate.
     *
     * @param integer $rate
     *
     * @return AudioStreamInterface
     */
    public function rate(int $rate): AudioStreamInterface
    {
        return $this->addStreamOption('-ar', $rate);
    }

    /**
     * Set channels count.
     *
     * @param integer $channels
     *
     * @return AudioStreamInterface
     */
    public function channels(int $channels): AudioStreamInterface
    {
        return $this->addStreamOption('-ac', $channels);
    }

    /**
     * Set codec.
     *
     * @param string $codec
     *
     * @return AudioStreamInterface
     */
    public function codec(string $codec): AudioStreamInterface
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
     * @return AudioStreamInterface
     */
    public function profile(string $profile): AudioStreamInterface
    {
        return $this->addStreamOption('-profile', $profile);
    }

    /**
     * Set bitrate.
     *
     * @param string $bitrate
     *
     * @return AudioStreamInterface
     */
    public function bitrate(string $bitrate): AudioStreamInterface
    {
        return $this->addStreamOption('-b', $bitrate);
    }

    /**
     * Set volume.
     *
     * @param integer $volume
     *
     * @return AudioStreamInterface
     */
    public function volume(int $volume): AudioStreamInterface
    {
        return $this->addOption('-vol', $volume);
    }

    /**
     * Set filter graph as a string.
     *
     * @param string $filterGraph
     *
     * @return AudioStreamInterface
     */
    public function filter(string $filterGraph): AudioStreamInterface
    {
        return $this->addOption('-af', $filterGraph);
    }

    /**
     * Set sample format.
     *
     * @param string $format
     *
     * @return AudioStreamInterface
     */
    public function sampleFormat(string $format): AudioStreamInterface
    {
        return $this->addStreamOption('-sample_fmt', $format);
    }

    /**
     * Set channel layout.
     *
     * @param string $layout
     *
     * @return AudioStreamInterface
     */
    public function channelLayout(string $layout): AudioStreamInterface
    {
        return $this->addOption('-channel_layout', $layout);
    }
}
