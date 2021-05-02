<?php

namespace Javer\FfmpegTransformer\File;

use Javer\FfmpegTransformer\BuilderInterface;
use Javer\FfmpegTransformer\Command\CommandInterface;
use Javer\FfmpegTransformer\Filter\Graph\FilterGraphInterface;
use Javer\FfmpegTransformer\Stream\AudioStreamInterface;
use Javer\FfmpegTransformer\Stream\StreamInterface;
use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

interface FileInterface extends BuilderInterface
{
    /**
     * Set format.
     *
     * Option: -f fmt
     *
     * @param string $format
     *
     * @return static
     */
    public function format(string $format): static;

    /**
     * Set codec.
     *
     * Option: -c codec
     *
     * @param string $codec
     *
     * @return static
     */
    public function codec(string $codec): static;

    /**
     * Set preset.
     *
     * Option: -pre preset
     *
     * @param string $preset
     *
     * @return static
     */
    public function preset(string $preset): static;

    /**
     * Set duration.
     *
     * Option: -t duration
     *
     * @param float $time
     *
     * @return static
     */
    public function duration(float $time): static;

    /**
     * Set to_time
     *
     * Option: -to time_stop
     *
     * @param float $timeStop
     *
     * @return static
     */
    public function toTime(float $timeStop): static;

    /**
     * Set file size.
     *
     * Option: -fs limit_size
     *
     * @param int $size
     *
     * @return static
     */
    public function filesize(int $size): static;

    /**
     * Set start time.
     *
     * Option: -ss time
     *
     * @param float $time
     *
     * @return static
     */
    public function startTime(float $time): static;

    /**
     * Set start time from the end.
     *
     * Option: -sseof time
     *
     * @param float $time
     *
     * @return static
     */
    public function startTimeFromEnd(float $time): static;

    /**
     * Seek timestamp.
     *
     * Option: -seek_timestamp
     *
     * @return static
     */
    public function seekTimestamp(): static;

    /**
     * Set timestamp.
     *
     * Option: -timestamp time
     *
     * @param string $time
     *
     * @return static
     */
    public function timestamp(string $time): static;

    /**
     * Set metadata value.
     *
     * Option: -metadata string=string
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function metadata(string $name, string $value): static;

    /**
     * Set target type.
     *
     * Option: -target type
     *
     * @param string $type
     *
     * @return static
     */
    public function target(string $type): static;

    /**
     * Apad.
     *
     * Option: -apad
     *
     * @return static
     */
    public function apad(): static;

    /**
     * Set frames number.
     *
     * Option: -frames number
     *
     * @param int $number
     *
     * @return static
     */
    public function frames(int $number): static;

    /**
     * Set filter script filename.
     *
     * Option: -filter_script filename
     *
     * @param string $filename
     *
     * @return static
     */
    public function filterScript(string $filename): static;

    /**
     * Reinitialize filter.
     *
     * Option: -reinit_filter
     *
     * @return static
     */
    public function reinitFilter(): static;

    /**
     * Discard.
     *
     * Option: -discard
     *
     * @return static
     */
    public function discard(): static;

    /**
     * Disposition.
     *
     * Option: -disposition
     *
     * @return static
     */
    public function disposition(): static;

    /**
     * Accurate seek.
     *
     * Option: -accurate_seek
     *
     * @return static
     */
    public function accurateSeek(): static;

    /**
     * Shortest output.
     *
     * Option: -shortest
     *
     * @return static
     */
    public function shortest(): static;

    /**
     * Set profile.
     *
     * Option: -profile profile
     *
     * @param string $profile
     *
     * @return static
     */
    public function profile(string $profile): static;

    /**
     * Attach file as a stream.
     *
     * Option: -attach filename
     *
     * @param string $filename
     *
     * @return static
     */
    public function attach(string $filename): static;

    /**
     * Move header to the start of the file.
     *
     * Option: -movflags faststart
     *
     * @return static
     */
    public function moveHeaderToStart(): static;

    /**
     * Force loop over input file sequence.
     *
     * Option: -loop 1
     *
     * @param bool $flag
     *
     * @return static
     */
    public function loop(bool $flag = true): static;

    /**
     * Add video stream.
     *
     * @param VideoStreamInterface|null $mapVideoStream
     *
     * @return VideoStreamInterface
     */
    public function addVideoStream(?VideoStreamInterface $mapVideoStream = null): VideoStreamInterface;

    /**
     * Add audio stream.
     *
     * @param AudioStreamInterface|null $mapAudioStream
     *
     * @return AudioStreamInterface
     */
    public function addAudioStream(?AudioStreamInterface $mapAudioStream = null): AudioStreamInterface;

    /**
     * Get video stream by number.
     *
     * @param int $number
     *
     * @return VideoStreamInterface
     */
    public function getVideoStream(int $number = 0): VideoStreamInterface;

    /**
     * Get audio stream by number.
     *
     * @param int $number
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
     * @return int|null
     */
    public function getStreamNumber(StreamInterface $stream): ?int;

    /**
     * Move stream to the given position (stream index) in the output file.
     *
     * @param StreamInterface $stream
     * @param int             $position
     *
     * @return static
     */
    public function moveStreamToPosition(StreamInterface $stream, int $position): static;

    /**
     * Remove stream.
     *
     * @param StreamInterface $stream
     *
     * @return static
     */
    public function removeStream(StreamInterface $stream): static;

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
     * @return static
     */
    public function addOption(string $name, string $argument = ''): static;

    /**
     * Return to command.
     *
     * @return CommandInterface
     */
    public function end(): CommandInterface;
}
