<?php

namespace Javer\FfmpegTransformer\Stream;

use Javer\FfmpegTransformer\File\FileInterface;

class AudioStream extends Stream implements AudioStreamInterface
{
    /**
     * AudioStream constructor.
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
        parent::__construct($file, $name, StreamInterface::TYPE_AUDIO, $isInput, $isMapped);
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
     * Disable audio stream.
     *
     * @return static
     */
    public function disable(): static
    {
        $this->isCustomCodec = true;

        return $this->addOption('-an');
    }

    /**
     * Map audio stream.
     *
     * @param AudioStreamInterface $stream
     *
     * @return static
     */
    public function map(AudioStreamInterface $stream): static
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
     * Set quality.
     *
     * @param int $quality
     *
     * @return static
     */
    public function quality(int $quality): static
    {
        return $this->addStreamOption('-q', (string) $quality);
    }

    /**
     * Set rate.
     *
     * @param int $rate
     *
     * @return static
     */
    public function rate(int $rate): static
    {
        return $this->addStreamOption('-ar', (string) $rate);
    }

    /**
     * Set channels count.
     *
     * @param int $channels
     *
     * @return static
     */
    public function channels(int $channels): static
    {
        return $this->addStreamOption('-ac', (string) $channels);
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
     * Set volume.
     *
     * @param int $volume
     *
     * @return static
     */
    public function volume(int $volume): static
    {
        return $this->addOption('-vol', (string) $volume);
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
        return $this->addOption('-af', $filterGraph);
    }

    /**
     * Set sample format.
     *
     * @param string $format
     *
     * @return static
     */
    public function sampleFormat(string $format): static
    {
        return $this->addStreamOption('-sample_fmt', $format);
    }

    /**
     * Set channel layout.
     *
     * @param string $layout
     *
     * @return static
     */
    public function channelLayout(string $layout): static
    {
        return $this->addOption('-channel_layout', $layout);
    }
}
