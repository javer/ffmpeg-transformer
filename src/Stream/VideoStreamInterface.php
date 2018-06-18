<?php

namespace Javer\FfmpegTransformer\Stream;

/**
 * Interface VideoStreamInterface
 *
 * @package Javer\FfmpegTransformer\Stream
 */
interface VideoStreamInterface extends StreamInterface
{
    /**
     * Add an option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return VideoStreamInterface
     */
    public function addOption(string $name, string $argument = ''): VideoStreamInterface;

    /**
     * Add per-stream option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return VideoStreamInterface
     */
    public function addStreamOption(string $name, string $argument = ''): VideoStreamInterface;

    /**
     * Copy stream "as is".
     *
     * Option: -vcodec copy
     *
     * @return VideoStreamInterface
     */
    public function copy(): VideoStreamInterface;

    /**
     * Disable video stream.
     *
     * Option: -vn
     *
     * @return VideoStreamInterface
     */
    public function disable(): VideoStreamInterface;

    /**
     * Map video stream.
     *
     * Option: -map [-]input_file_id[:stream_specifier][,sync_file_id[:stream_s]]
     *
     * @param VideoStreamInterface $stream
     *
     * @return VideoStreamInterface
     */
    public function map(VideoStreamInterface $stream): VideoStreamInterface;

    /**
     * Set frames number.
     *
     * Option: -vframes number
     *
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function frames(int $number): VideoStreamInterface;

    /**
     * Set frame rate.
     *
     * Option: -r rate
     *
     * @param float $rate
     *
     * @return VideoStreamInterface
     */
    public function frameRate(float $rate): VideoStreamInterface;

    /**
     * Set frame size.
     *
     * Option: -s size
     *
     * @param string $size
     *
     * @return VideoStreamInterface
     */
    public function frameSize(string $size): VideoStreamInterface;

    /**
     * Set aspect ratio.
     *
     * Option: -aspect aspect
     *
     * @param string $aspect
     *
     * @return VideoStreamInterface
     */
    public function aspectRatio(string $aspect): VideoStreamInterface;

    /**
     * Set bits per raw sample.
     *
     * Option: -bits_per_raw_sample
     *
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function bitsPerRawSample(int $number): VideoStreamInterface;

    /**
     * Set codec.
     *
     * Option: -vcodec codec
     *
     * @param string $codec
     *
     * @return VideoStreamInterface
     */
    public function codec(string $codec): VideoStreamInterface;

    /**
     * Set profile.
     *
     * Option: -profile:v profile
     *
     * @param string $profile
     *
     * @return VideoStreamInterface
     */
    public function profile(string $profile): VideoStreamInterface;

    /**
     * Set preset.
     *
     * Option: -preset preset
     *
     * @param string $preset
     *
     * @return VideoStreamInterface
     */
    public function preset(string $preset): VideoStreamInterface;

    /**
     * Set time code.
     *
     * Option: -timecode hh:mm:ss[:;.]ff
     *
     * @param string $timecode
     *
     * @return VideoStreamInterface
     */
    public function timeCode(string $timecode): VideoStreamInterface;

    /**
     * Set pass.
     *
     * Option: -pass n
     *
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function pass(int $number): VideoStreamInterface;

    /**
     * Set filter graph as a string.
     *
     * Option: -vf filter_graph
     *
     * @param string $filterGraph
     *
     * @return VideoStreamInterface
     */
    public function filter(string $filterGraph): VideoStreamInterface;

    /**
     * Set bitrate.
     *
     * Option: -b:v bitrate
     *
     * @param string $bitrate
     *
     * @return VideoStreamInterface
     */
    public function bitrate(string $bitrate): VideoStreamInterface;

    /**
     * Set pixel format.
     *
     * Option: -pix_fmt format
     *
     * @param string $format
     *
     * @return VideoStreamInterface
     */
    public function pixelFormat(string $format): VideoStreamInterface;

    /**
     * Set keyframe interval.
     *
     * Option: -g interval
     *
     * @param integer $interval
     *
     * @return VideoStreamInterface
     */
    public function keyframeInterval(int $interval): VideoStreamInterface;
}
