<?php

namespace Javer\FfmpegTransformer\Stream;

/**
 * Interface AudioStreamInterface
 *
 * @package Javer\FfmpegTransformer\Stream
 */
interface AudioStreamInterface extends StreamInterface
{
    /**
     * Add an option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return AudioStreamInterface
     */
    public function addOption(string $name, string $argument = ''): AudioStreamInterface;

    /**
     * Add per-stream option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return AudioStreamInterface
     */
    public function addStreamOption(string $name, string $argument = ''): AudioStreamInterface;

    /**
     * Copy stream "as is".
     *
     * Option: -acodec copy
     *
     * @return AudioStreamInterface
     */
    public function copy(): AudioStreamInterface;

    /**
     * Disable audio stream.
     *
     * Option: -an
     *
     * @return AudioStreamInterface
     */
    public function disable(): AudioStreamInterface;

    /**
     * Map audio stream.
     *
     * Option: -map [-]input_file_id[:stream_specifier][,sync_file_id[:stream_s]]
     *
     * @param AudioStreamInterface $stream
     *
     * @return AudioStreamInterface
     */
    public function map(AudioStreamInterface $stream): AudioStreamInterface;

    /**
     * Set frames number.
     *
     * Option: -aframes number
     *
     * @param integer $number
     *
     * @return AudioStreamInterface
     */
    public function frames(int $number): AudioStreamInterface;

    /**
     * Set quality.
     *
     * Option: -aq quality
     *
     * @param integer $quality
     *
     * @return AudioStreamInterface
     */
    public function quality(int $quality): AudioStreamInterface;

    /**
     * Set rate.
     *
     * Option: -ar rate
     *
     * @param integer $rate
     *
     * @return AudioStreamInterface
     */
    public function rate(int $rate): AudioStreamInterface;

    /**
     * Set channels count.
     *
     * Option: -ac channels
     *
     * @param integer $channels
     *
     * @return AudioStreamInterface
     */
    public function channels(int $channels): AudioStreamInterface;

    /**
     * Set codec.
     *
     * Option: -acodec codec
     *
     * @param string $codec
     *
     * @return AudioStreamInterface
     */
    public function codec(string $codec): AudioStreamInterface;

    /**
     * Set profile.
     *
     * Option: -profile:a profile
     *
     * @param string $profile
     *
     * @return AudioStreamInterface
     */
    public function profile(string $profile): AudioStreamInterface;

    /**
     * Set bitrate.
     *
     * Option: -b:a bitrate
     *
     * @param string $bitrate
     *
     * @return AudioStreamInterface
     */
    public function bitrate(string $bitrate): AudioStreamInterface;

    /**
     * Set volume.
     *
     * Option: -vol volume
     *
     * @param integer $volume
     *
     * @return AudioStreamInterface
     */
    public function volume(int $volume): AudioStreamInterface;

    /**
     * Set filter graph as a string.
     *
     * Option: -af filter_graph
     *
     * @param string $filterGraph
     *
     * @return AudioStreamInterface
     */
    public function filter(string $filterGraph): AudioStreamInterface;

    /**
     * Set sample format.
     *
     * Option: -sample_fmt format
     *
     * @param string $format
     *
     * @return AudioStreamInterface
     */
    public function sampleFormat(string $format): AudioStreamInterface;

    /**
     * Set channel layout.
     *
     * Option: -channel_layout layout
     *
     * @param string $layout
     *
     * @return AudioStreamInterface
     */
    public function channelLayout(string $layout): AudioStreamInterface;
}
