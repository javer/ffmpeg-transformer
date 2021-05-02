<?php

namespace Javer\FfmpegTransformer\Stream;

interface AudioStreamInterface extends StreamInterface
{
    /**
     * Add an option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return static
     */
    public function addOption(string $name, string $argument = ''): static;

    /**
     * Add per-stream option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return static
     */
    public function addStreamOption(string $name, string $argument = ''): static;

    /**
     * Copy stream "as is".
     *
     * Option: -acodec copy
     *
     * @return static
     */
    public function copy(): static;

    /**
     * Disable audio stream.
     *
     * Option: -an
     *
     * @return static
     */
    public function disable(): static;

    /**
     * Map audio stream.
     *
     * Option: -map [-]input_file_id[:stream_specifier][,sync_file_id[:stream_s]]
     *
     * @param AudioStreamInterface $stream
     *
     * @return static
     */
    public function map(AudioStreamInterface $stream): static;

    /**
     * Set frames number.
     *
     * Option: -aframes number
     *
     * @param int $number
     *
     * @return static
     */
    public function frames(int $number): static;

    /**
     * Set quality.
     *
     * Option: -aq quality
     *
     * @param int $quality
     *
     * @return static
     */
    public function quality(int $quality): static;

    /**
     * Set rate.
     *
     * Option: -ar rate
     *
     * @param int $rate
     *
     * @return static
     */
    public function rate(int $rate): static;

    /**
     * Set channels count.
     *
     * Option: -ac channels
     *
     * @param int $channels
     *
     * @return static
     */
    public function channels(int $channels): static;

    /**
     * Set codec.
     *
     * Option: -acodec codec
     *
     * @param string $codec
     *
     * @return static
     */
    public function codec(string $codec): static;

    /**
     * Set profile.
     *
     * Option: -profile:a profile
     *
     * @param string $profile
     *
     * @return static
     */
    public function profile(string $profile): static;

    /**
     * Set bitrate.
     *
     * Option: -b:a bitrate
     *
     * @param string $bitrate
     *
     * @return static
     */
    public function bitrate(string $bitrate): static;

    /**
     * Set volume.
     *
     * Option: -vol volume
     *
     * @param int $volume
     *
     * @return static
     */
    public function volume(int $volume): static;

    /**
     * Set filter graph as a string.
     *
     * Option: -af filter_graph
     *
     * @param string $filterGraph
     *
     * @return static
     */
    public function filter(string $filterGraph): static;

    /**
     * Set sample format.
     *
     * Option: -sample_fmt format
     *
     * @param string $format
     *
     * @return static
     */
    public function sampleFormat(string $format): static;

    /**
     * Set channel layout.
     *
     * Option: -channel_layout layout
     *
     * @param string $layout
     *
     * @return static
     */
    public function channelLayout(string $layout): static;
}
