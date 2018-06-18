<?php

namespace Javer\FfmpegTransformer\File;

use Javer\FfmpegTransformer\BuilderInterface;
use Javer\FfmpegTransformer\Command\CommandInterface;
use Javer\FfmpegTransformer\Filter\Graph\FilterGraphInterface;
use Javer\FfmpegTransformer\Stream\AudioStreamInterface;
use Javer\FfmpegTransformer\Stream\StreamInterface;
use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

/**
 * Interface FileInterface
 *
 * @package Javer\FfmpegTransformer\File
 */
interface FileInterface extends BuilderInterface
{
    /**
     * Set format.
     *
     * Option: -f fmt
     *
     * @param string $format
     *
     * @return FileInterface
     */
    public function format(string $format): FileInterface;

    /**
     * Set codec.
     *
     * Option: -c codec
     *
     * @param string $codec
     *
     * @return FileInterface
     */
    public function codec(string $codec): FileInterface;

    /**
     * Set preset.
     *
     * Option: -pre preset
     *
     * @param string $preset
     *
     * @return FileInterface
     */
    public function preset(string $preset): FileInterface;

    /**
     * Set duration.
     *
     * Option: -t duration
     *
     * @param float $time
     *
     * @return FileInterface
     */
    public function duration(float $time): FileInterface;

    /**
     * Set to_time
     *
     * Option: -to time_stop
     *
     * @param float $timeStop
     *
     * @return FileInterface
     */
    public function toTime(float $timeStop): FileInterface;

    /**
     * Set file size.
     *
     * Option: -fs limit_size
     *
     * @param integer $size
     *
     * @return FileInterface
     */
    public function filesize(int $size): FileInterface;

    /**
     * Set start time.
     *
     * Option: -ss time
     *
     * @param float $time
     *
     * @return FileInterface
     */
    public function startTime(float $time): FileInterface;

    /**
     * Set start time from the end.
     *
     * Option: -sseof time
     *
     * @param float $time
     *
     * @return FileInterface
     */
    public function startTimeFromEnd(float $time): FileInterface;

    /**
     * Seek timestamp.
     *
     * Option: -seek_timestamp
     *
     * @return FileInterface
     */
    public function seekTimestamp(): FileInterface;

    /**
     * Set timestamp.
     *
     * Option: -timestamp time
     *
     * @param string $time
     *
     * @return FileInterface
     */
    public function timestamp(string $time): FileInterface;

    /**
     * Set metadata value.
     *
     * Option: -metadata string=string
     *
     * @param string $name
     * @param string $value
     *
     * @return FileInterface
     */
    public function metadata(string $name, string $value): FileInterface;

    /**
     * Set target type.
     *
     * Option: -target type
     *
     * @param string $type
     *
     * @return FileInterface
     */
    public function target(string $type): FileInterface;

    /**
     * Apad.
     *
     * Option: -apad
     *
     * @return FileInterface
     */
    public function apad(): FileInterface;

    /**
     * Set frames number.
     *
     * Option: -frames number
     *
     * @param integer $number
     *
     * @return FileInterface
     */
    public function frames(int $number): FileInterface;

    /**
     * Set filter script filename.
     *
     * Option: -filter_script filename
     *
     * @param string $filename
     *
     * @return FileInterface
     */
    public function filterScript(string $filename): FileInterface;

    /**
     * Reinitialize filter.
     *
     * Option: -reinit_filter
     *
     * @return FileInterface
     */
    public function reinitFilter(): FileInterface;

    /**
     * Discard.
     *
     * Option: -discard
     *
     * @return FileInterface
     */
    public function discard(): FileInterface;

    /**
     * Disposition.
     *
     * Option: -disposition
     *
     * @return FileInterface
     */
    public function disposition(): FileInterface;

    /**
     * Accurate seek.
     *
     * Option: -accurate_seek
     *
     * @return FileInterface
     */
    public function accurateSeek(): FileInterface;

    /**
     * Shortest output.
     *
     * Option: -shortest
     *
     * @return FileInterface
     */
    public function shortest(): FileInterface;

    /**
     * Set profile.
     *
     * Option: -profile profile
     *
     * @param string $profile
     *
     * @return FileInterface
     */
    public function profile(string $profile): FileInterface;

    /**
     * Attach file as a stream.
     *
     * Option: -attach filename
     *
     * @param string $fiename
     *
     * @return FileInterface
     */
    public function attach(string $fiename): FileInterface;

    /**
     * Move header to the start of the file.
     *
     * Option: -movflags faststart
     *
     * @return FileInterface
     */
    public function moveHeaderToStart(): FileInterface;

    /**
     * Force loop over input file sequence.
     *
     * Option: -loop 1
     *
     * @param boolean $flag
     *
     * @return FileInterface
     */
    public function loop(bool $flag = true): FileInterface;

    /**
     * Add video stream.
     *
     * @param VideoStreamInterface $mapVideoStream
     *
     * @return VideoStreamInterface
     */
    public function addVideoStream(VideoStreamInterface $mapVideoStream = null): VideoStreamInterface;

    /**
     * Add audio stream.
     *
     * @param AudioStreamInterface $mapAudioStream
     *
     * @return AudioStreamInterface
     */
    public function addAudioStream(AudioStreamInterface $mapAudioStream = null): AudioStreamInterface;

    /**
     * Get video stream by number.
     *
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function getVideoStream(int $number = 0): VideoStreamInterface;

    /**
     * Get audio stream by number.
     *
     * @param integer $number
     *
     * @return AudioStreamInterface
     */
    public function getAudioStream(int $number = 0): AudioStreamInterface;

    /**
     * Create a video stream.
     *
     * @return VideoStreamInterface
     */
    public function createVideoStream(): VideoStreamInterface;

    /**
     * Create an audio stream.
     *
     * @return AudioStreamInterface
     */
    public function createAudioStream(): AudioStreamInterface;

    /**
     * Create a stream with the given type.
     *
     * @param string $type
     *
     * @return StreamInterface
     */
    public function createStream(string $type): StreamInterface;

    /**
     * Returns number of the stream in the file.
     *
     * @param StreamInterface $stream
     *
     * @return integer|null
     */
    public function getStreamNumber(StreamInterface $stream): ?int;

    /**
     * Move stream to the given position (stream index) in the output file.
     *
     * @param StreamInterface $stream
     * @param integer         $position
     *
     * @return FileInterface
     */
    public function moveStreamToPosition(StreamInterface $stream, int $position): FileInterface;

    /**
     * Remove stream.
     *
     * @param StreamInterface $stream
     *
     * @return FileInterface
     */
    public function removeStream(StreamInterface $stream): FileInterface;

    /**
     * Add filter graph.
     *
     * Option: -filter_complex filter_graph
     *
     * @return FilterGraphInterface
     */
    public function filter(): FilterGraphInterface;

    /**
     * Returns name of the file.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Add an option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return FileInterface
     */
    public function addOption(string $name, string $argument = ''): FileInterface;

    /**
     * Return to command.
     *
     * @return CommandInterface
     */
    public function end(): CommandInterface;
}
