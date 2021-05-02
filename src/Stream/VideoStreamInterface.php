<?php

namespace Javer\FfmpegTransformer\Stream;

interface VideoStreamInterface extends StreamInterface
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
     * Option: -vcodec copy
     *
     * @return static
     */
    public function copy(): static;

    /**
     * Disable video stream.
     *
     * Option: -vn
     *
     * @return static
     */
    public function disable(): static;

    /**
     * Map video stream.
     *
     * Option: -map [-]input_file_id[:stream_specifier][,sync_file_id[:stream_s]]
     *
     * @param VideoStreamInterface $stream
     *
     * @return static
     */
    public function map(VideoStreamInterface $stream): static;

    /**
     * Set frames number.
     *
     * Option: -vframes number
     *
     * @param int $number
     *
     * @return static
     */
    public function frames(int $number): static;

    /**
     * Set frame rate.
     *
     * Option: -r rate
     *
     * @param float $rate
     *
     * @return static
     */
    public function frameRate(float $rate): static;

    /**
     * Set frame size.
     *
     * Option: -s size
     *
     * @param string $size
     *
     * @return static
     */
    public function frameSize(string $size): static;

    /**
     * Set aspect ratio.
     *
     * Option: -aspect aspect
     *
     * @param string $aspect
     *
     * @return static
     */
    public function aspectRatio(string $aspect): static;

    /**
     * Set bits per raw sample.
     *
     * Option: -bits_per_raw_sample
     *
     * @param int $number
     *
     * @return static
     */
    public function bitsPerRawSample(int $number): static;

    /**
     * Set codec.
     *
     * Option: -vcodec codec
     *
     * @param string $codec
     *
     * @return static
     */
    public function codec(string $codec): static;

    /**
     * Set profile.
     *
     * Option: -profile:v profile
     *
     * @param string $profile
     *
     * @return static
     */
    public function profile(string $profile): static;

    /**
     * Set preset.
     *
     * Option: -preset preset
     *
     * @param string $preset
     *
     * @return static
     */
    public function preset(string $preset): static;

    /**
     * Set time code.
     *
     * Option: -timecode hh:mm:ss[:;.]ff
     *
     * @param string $timecode
     *
     * @return static
     */
    public function timeCode(string $timecode): static;

    /**
     * Set pass.
     *
     * Option: -pass n
     *
     * @param int $number
     *
     * @return static
     */
    public function pass(int $number): static;

    /**
     * Set filter graph as a string.
     *
     * Option: -vf filter_graph
     *
     * @param string $filterGraph
     *
     * @return static
     */
    public function filter(string $filterGraph): static;

    /**
     * Set bitrate.
     *
     * Option: -b:v bitrate
     *
     * @param string $bitrate
     *
     * @return static
     */
    public function bitrate(string $bitrate): static;

    /**
     * Set maxBitrate.
     *
     * Option: -maxrate:v maxBitrate
     *
     * @param string $maxBitrate
     *
     * @return static
     */
    public function maxBitrate(string $maxBitrate): static;

    /**
     * Set minBitrate.
     *
     * Option: -minrate:v minBitrate
     *
     * @param string $minBitrate
     *
     * @return static
     */
    public function minBitrate(string $minBitrate): static;

    /**
     * Set buffer size.
     *
     * Option: -bufsize:v bufferSize
     *
     * @param string $bufferSize
     *
     * @return static
     */
    public function bufferSize(string $bufferSize): static;

    /**
     * Set crf.
     *
     * Option: -crf:v bitrate
     *
     * @param int $crf
     *
     * @return static
     */
    public function crf(int $crf): static;

    /**
     * Set pixel format.
     *
     * Option: -pix_fmt format
     *
     * @param string $format
     *
     * @return static
     */
    public function pixelFormat(string $format): static;

    /**
     * Set keyframe interval.
     *
     * Option: -g interval
     *
     * @param int $interval
     *
     * @return static
     */
    public function keyframeInterval(int $interval): static;
}
