<?php

namespace Javer\FfmpegTransformer\File;

use Javer\FfmpegTransformer\Command\CommandInterface;
use Javer\FfmpegTransformer\Filter\Graph\FilterGraph;
use Javer\FfmpegTransformer\Filter\Graph\FilterGraphInterface;
use Javer\FfmpegTransformer\Stream\AudioStream;
use Javer\FfmpegTransformer\Stream\AudioStreamInterface;
use Javer\FfmpegTransformer\Stream\StreamInterface;
use Javer\FfmpegTransformer\Stream\VideoStream;
use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

/**
 * Class File
 *
 * @package Javer\FfmpegTransformer\File
 */
class File implements FileInterface
{
    /**
     * @var CommandInterface
     */
    protected $command;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $isInput;

    /**
     * @var string[]
     */
    protected $options = [];

    /**
     * @var VideoStreamInterface[]
     */
    protected $videoStreams = [];

    /**
     * @var AudioStreamInterface[]
     */
    protected $audioStreams = [];

    /**
     * @var FilterGraphInterface
     */
    protected $filterGraph = null;

    /**
     * @var integer
     */
    protected $streamsCounter = 0;

    /**
     * File constructor.
     *
     * @param CommandInterface $command
     * @param string           $filename
     * @param string           $name
     * @param boolean          $isInput
     */
    public function __construct(CommandInterface $command, $filename, $name = '', $isInput = false)
    {
        $this->command = $command;
        $this->filename = $filename;
        $this->name = (string) $name;
        $this->isInput = $isInput;
    }

    /**
     * Add an option.
     *
     * @param string $name
     * @param string $argument
     *
     * @return FileInterface
     */
    public function addOption(string $name, string $argument = ''): FileInterface
    {
        $this->options[] = $name;

        if (strlen($argument) > 0) {
            $this->options[] = $argument;
        }

        return $this;
    }

    /**
     * Build command.
     *
     * @return array
     */
    public function build(): array
    {
        $options = $this->options;

        if ($this->filterGraph) {
            $options = array_merge($options, $this->filterGraph->build());
        }

        foreach ($this->videoStreams as $videoStream) {
            $options = array_merge($options, $videoStream->build());
        }

        foreach ($this->audioStreams as $audioStream) {
            $options = array_merge($options, $audioStream->build());
        }

        if ($this->isInput) {
            $options[] = '-i';
        }

        $options[] = $this->filename;

        return $options;
    }

    /**
     * Returns a string representation of the file.
     *
     * @return string
     */
    public function __toString(): string
    {
        return implode(' ', array_map('escapeshellarg', $this->build()));
    }

    /**
     * Clones the current file.
     */
    public function __clone()
    {
        $this->options = [];
        $this->videoStreams = [];
        $this->audioStreams = [];
        $this->filterGraph = null;
    }

    /**
     * Set format.
     *
     * @param string $format
     *
     * @return FileInterface
     */
    public function format(string $format): FileInterface
    {
        return $this->addOption('-f', $format);
    }

    /**
     * Set codec.
     *
     * @param string $codec
     *
     * @return FileInterface
     */
    public function codec(string $codec): FileInterface
    {
        return $this->addOption('-c', $codec);
    }

    /**
     * Set preset.
     *
     * @param string $preset
     *
     * @return FileInterface
     */
    public function preset(string $preset): FileInterface
    {
        return $this->addOption('-pre', $preset);
    }

    /**
     * Set duration.
     *
     * @param float $time
     *
     * @return FileInterface
     */
    public function duration(float $time): FileInterface
    {
        return $this->addOption('-t', $time);
    }

    /**
     * Set to_time
     *
     * @param float $timeStop
     *
     * @return FileInterface
     */
    public function toTime(float $timeStop): FileInterface
    {
        return $this->addOption('-to', $timeStop);
    }

    /**
     * Set file size.
     *
     * @param integer $size
     *
     * @return FileInterface
     */
    public function filesize(int $size): FileInterface
    {
        return $this->addOption('-fs', $size);
    }

    /**
     * Set start time.
     *
     * @param float $time
     *
     * @return FileInterface
     */
    public function startTime(float $time): FileInterface
    {
        return $this->addOption('-ss', $time);
    }

    /**
     * Set start time from the end.
     *
     * @param float $time
     *
     * @return FileInterface
     */
    public function startTimeFromEnd(float $time): FileInterface
    {
        return $this->addOption('-sseof', $time);
    }

    /**
     * Seek timestamp.
     *
     * @return FileInterface
     */
    public function seekTimestamp(): FileInterface
    {
        return $this->addOption('-seek_timestamp');
    }

    /**
     * Set timestamp.
     *
     * @param string $time
     *
     * @return FileInterface
     */
    public function timestamp(string $time): FileInterface
    {
        return $this->addOption('-timestamp', $time);
    }

    /**
     * Set metadata value.
     *
     * @param string $name
     * @param string $value
     *
     * @return FileInterface
     */
    public function metadata(string $name, string $value): FileInterface
    {
        return $this->addOption('-metadata', sprintf('%s=%s', $name, $value));
    }

    /**
     * Set target type.
     *
     * @param string $type
     *
     * @return FileInterface
     */
    public function target(string $type): FileInterface
    {
        return $this->addOption('-target', $type);
    }

    /**
     * Apad.
     *
     * @return FileInterface
     */
    public function apad(): FileInterface
    {
        return $this->addOption('-apad');
    }

    /**
     * Set frames number.
     *
     * @param integer $number
     *
     * @return FileInterface
     */
    public function frames(int $number): FileInterface
    {
        return $this->addOption('-frames', $number);
    }

    /**
     * Set filter script filename.
     *
     * @param string $filename
     *
     * @return FileInterface
     */
    public function filterScript(string $filename): FileInterface
    {
        return $this->addOption('-filter_script', $filename);
    }

    /**
     * Reinitialize filter.
     *
     * @return FileInterface
     */
    public function reinitFilter(): FileInterface
    {
        return $this->addOption('-reinit_filter');
    }

    /**
     * Discard.
     *
     * @return FileInterface
     */
    public function discard(): FileInterface
    {
        return $this->addOption('-discard');
    }

    /**
     * Disposition.
     *
     * @return FileInterface
     */
    public function disposition(): FileInterface
    {
        return $this->addOption('-disposition');
    }

    /**
     * Accurate seek.
     *
     * @return FileInterface
     */
    public function accurateSeek(): FileInterface
    {
        return $this->addOption('-accurate_seek');
    }

    /**
     * Shortest output.
     *
     * @return FileInterface
     */
    public function shortest(): FileInterface
    {
        return $this->addOption('-shortest');
    }

    /**
     * Set profile.
     *
     * @param string $profile
     *
     * @return FileInterface
     */
    public function profile(string $profile): FileInterface
    {
        return $this->addOption('-profile', $profile);
    }

    /**
     * Attach file as a stream.
     *
     * @param string $fiename
     *
     * @return FileInterface
     *
     * @throws \LogicException
     */
    public function attach(string $fiename): FileInterface
    {
        if ($this->isInput) {
            throw new \LogicException('Attach option can be used only for output files');
        }

        return $this->addOption('-attach', $fiename);
    }

    /**
     * Move header to the start of the file.
     *
     * @return FileInterface
     *
     * @throws \LogicException
     */
    public function moveHeaderToStart(): FileInterface
    {
        if ($this->isInput) {
            throw new \LogicException('Movflags option can be used only for output files');
        }

        return $this->addOption('-movflags', 'faststart');
    }

    /**
     * Force loop over input file sequence.
     *
     * Option: -loop 1
     *
     * @param boolean $flag
     *
     * @return FileInterface
     */
    public function loop(bool $flag = true): FileInterface
    {
        return $this->addOption('-loop', $flag ? 1 : 0);
    }

    /**
     * Add video stream.
     *
     * @param VideoStreamInterface $mapVideoStream
     *
     * @return VideoStreamInterface
     *
     * @throws \LogicException
     */
    public function addVideoStream(VideoStreamInterface $mapVideoStream = null): VideoStreamInterface
    {
        if ($this->isInput) {
            throw new \LogicException('AddVideoStream can be used only for output files, use getVideoStream for input');
        }

        $videoStream = new VideoStream($this);

        $this->videoStreams[] = $videoStream;

        if ($mapVideoStream) {
            $videoStream->map($mapVideoStream);
        }

        return $videoStream;
    }

    /**
     * Add audio stream.
     *
     * @param AudioStreamInterface $mapAudioStream
     *
     * @return AudioStreamInterface
     *
     * @throws \LogicException
     */
    public function addAudioStream(AudioStreamInterface $mapAudioStream = null): AudioStreamInterface
    {
        if ($this->isInput) {
            throw new \LogicException('AddAudioStream can be used only for output files, use getAudioStream for input');
        }

        $audioStream = new AudioStream($this);

        $this->audioStreams[] = $audioStream;

        if ($mapAudioStream) {
            $audioStream->map($mapAudioStream);
        }

        return $audioStream;
    }

    /**
     * Get video stream by number.
     *
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function getVideoStream(int $number = 0): VideoStreamInterface
    {
        if (!isset($this->videoStreams[$number])) {
            $this->videoStreams[$number] = new VideoStream($this, $number, '', $this->isInput);
        }

        return $this->videoStreams[$number];
    }

    /**
     * Get audio stream by number.
     *
     * @param integer $number
     *
     * @return AudioStreamInterface
     */
    public function getAudioStream(int $number = 0): AudioStreamInterface
    {
        if (!isset($this->audioStreams[$number])) {
            $this->audioStreams[$number] = new AudioStream($this, $number, '', $this->isInput);
        }

        return $this->audioStreams[$number];
    }

    /**
     * Create a video stream.
     *
     * @return VideoStreamInterface
     *
     * @throws \LogicException
     */
    public function createVideoStream(): VideoStreamInterface
    {
        if ($this->isInput) {
            throw new \LogicException('CreateVideoStream can only be used for output files');
        }

        $streamName = sprintf('v%s_%d', $this->getName(), $this->streamsCounter++);

        return new VideoStream($this, $streamName, '', false, false);
    }

    /**
     * Create an audio stream.
     *
     * @return AudioStreamInterface
     *
     * @throws \LogicException
     */
    public function createAudioStream(): AudioStreamInterface
    {
        if ($this->isInput) {
            throw new \LogicException('CreateAudioStream can only be used for output files');
        }

        $streamName = sprintf('a%s_%d', $this->getName(), $this->streamsCounter++);

        return new AudioStream($this, $streamName, '', false, false);
    }

    /**
     * Create a stream with the given type.
     *
     * @param string $type
     *
     * @return StreamInterface
     *
     * @throws \LogicException
     */
    public function createStream(string $type): StreamInterface
    {
        switch ($type) {
            case StreamInterface::TYPE_VIDEO:
                return $this->createVideoStream();

            case StreamInterface::TYPE_AUDIO:
                return $this->createAudioStream();

            default:
                throw new \LogicException(sprintf('Unknown stream type: %s', $type));
        }
    }

    /**
     * Returns number of the stream in the file.
     *
     * @param StreamInterface $stream
     *
     * @return integer|null
     *
     * @throws \LogicException
     */
    public function getStreamNumber(StreamInterface $stream): ?int
    {
        switch ($stream->getType()) {
            case StreamInterface::TYPE_VIDEO:
                $this->videoStreams = array_values($this->videoStreams);
                $streamNumber = array_search($stream, $this->videoStreams, true);
                break;

            case StreamInterface::TYPE_AUDIO:
                $this->audioStreams = array_values($this->audioStreams);
                $streamNumber = array_search($stream, $this->audioStreams, true);
                break;

            default:
                throw new \LogicException(sprintf('Unknown stream type: %s', $stream->getType()));
        }

        return $streamNumber === false ? null : $streamNumber;
    }

    /**
     * Move stream to the given position (stream index) in the output file.
     *
     * @param StreamInterface $stream
     * @param integer         $position
     *
     * @return FileInterface
     *
     * @throws \LogicException
     */
    public function moveStreamToPosition(StreamInterface $stream, int $position): FileInterface
    {
        if ($stream->getInput()) {
            throw new \LogicException('You cannot reorder streams in the input file');
        }

        $streamNumber = $this->getStreamNumber($stream);

        if (is_null($streamNumber)) {
            throw new \LogicException(sprintf('Stream %s not found', $stream->getName()));
        }

        switch ($stream->getType()) {
            case StreamInterface::TYPE_VIDEO:
                $removed = array_splice($this->videoStreams, $streamNumber, 1);
                array_splice($this->videoStreams, $position, 0, $removed);
                break;

            case StreamInterface::TYPE_AUDIO:
                $removed = array_splice($this->audioStreams, $streamNumber, 1);
                array_splice($this->audioStreams, $position, 0, $removed);
                break;

            default:
                throw new \LogicException(sprintf('Unknown stream type: %s', $stream->getType()));
        }

        return $this;
    }

    /**
     * Remove stream.
     *
     * @param StreamInterface $stream
     *
     * @return FileInterface
     */
    public function removeStream(StreamInterface $stream): FileInterface
    {
        foreach ($this->videoStreams as $ndx => $videoStreamElement) {
            if ($videoStreamElement === $stream) {
                unset($this->videoStreams[$ndx]);
            }
        }

        foreach ($this->audioStreams as $ndx => $audioStreamElement) {
            if ($audioStreamElement === $stream) {
                unset($this->audioStreams[$ndx]);
            }
        }

        return $this;
    }

    /**
     * Add filter graph.
     *
     * @return FilterGraphInterface
     */
    public function filter(): FilterGraphInterface
    {
        if (is_null($this->filterGraph)) {
            $this->filterGraph = new FilterGraph($this);
        }

        return $this->filterGraph;
    }

    /**
     * Returns name of the file.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return to command.
     *
     * @return CommandInterface
     */
    public function end(): CommandInterface
    {
        return $this->command;
    }
}
