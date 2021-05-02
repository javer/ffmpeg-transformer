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
use LogicException;

class File implements FileInterface
{
    protected CommandInterface $command;

    protected string $filename;

    protected string $name;

    protected bool $isInput;

    /**
     * @var string[]
     */
    protected array $options = [];

    /**
     * @var VideoStreamInterface[]
     */
    protected array $videoStreams = [];

    /**
     * @var AudioStreamInterface[]
     */
    protected array $audioStreams = [];

    protected ?FilterGraphInterface $filterGraph = null;

    protected int $streamsCounter = 0;

    /**
     * File constructor.
     *
     * @param CommandInterface $command
     * @param string           $filename
     * @param string           $name
     * @param bool             $isInput
     */
    public function __construct(CommandInterface $command, string $filename, string $name = '', bool $isInput = false)
    {
        $this->command = $command;
        $this->filename = $filename;
        $this->name = $name;
        $this->isInput = $isInput;
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
        $this->options[] = $name;

        if ($argument !== '') {
            $this->options[] = $argument;
        }

        return $this;
    }

    /**
     * Build command.
     *
     * @return array<int, string>
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
     * @return static
     */
    public function format(string $format): static
    {
        return $this->addOption('-f', $format);
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
        return $this->addOption('-c', $codec);
    }

    /**
     * Set preset.
     *
     * @param string $preset
     *
     * @return static
     */
    public function preset(string $preset): static
    {
        return $this->addOption('-pre', $preset);
    }

    /**
     * Set duration.
     *
     * @param float $time
     *
     * @return static
     */
    public function duration(float $time): static
    {
        return $this->addOption('-t', (string) $time);
    }

    /**
     * Set to_time
     *
     * @param float $timeStop
     *
     * @return static
     */
    public function toTime(float $timeStop): static
    {
        return $this->addOption('-to', (string) $timeStop);
    }

    /**
     * Set file size.
     *
     * @param int $size
     *
     * @return static
     */
    public function filesize(int $size): static
    {
        return $this->addOption('-fs', (string) $size);
    }

    /**
     * Set start time.
     *
     * @param float $time
     *
     * @return static
     */
    public function startTime(float $time): static
    {
        return $this->addOption('-ss', (string) $time);
    }

    /**
     * Set start time from the end.
     *
     * @param float $time
     *
     * @return static
     */
    public function startTimeFromEnd(float $time): static
    {
        return $this->addOption('-sseof', (string) $time);
    }

    /**
     * Seek timestamp.
     *
     * @return static
     */
    public function seekTimestamp(): static
    {
        return $this->addOption('-seek_timestamp');
    }

    /**
     * Set timestamp.
     *
     * @param string $time
     *
     * @return static
     */
    public function timestamp(string $time): static
    {
        return $this->addOption('-timestamp', $time);
    }

    /**
     * Set metadata value.
     *
     * @param string $name
     * @param string $value
     *
     * @return static
     */
    public function metadata(string $name, string $value): static
    {
        return $this->addOption('-metadata', sprintf('%s=%s', $name, $value));
    }

    /**
     * Set target type.
     *
     * @param string $type
     *
     * @return static
     */
    public function target(string $type): static
    {
        return $this->addOption('-target', $type);
    }

    /**
     * Apad.
     *
     * @return static
     */
    public function apad(): static
    {
        return $this->addOption('-apad');
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
        return $this->addOption('-frames', (string) $number);
    }

    /**
     * Set filter script filename.
     *
     * @param string $filename
     *
     * @return static
     */
    public function filterScript(string $filename): static
    {
        return $this->addOption('-filter_script', $filename);
    }

    /**
     * Reinitialize filter.
     *
     * @return static
     */
    public function reinitFilter(): static
    {
        return $this->addOption('-reinit_filter');
    }

    /**
     * Discard.
     *
     * @return static
     */
    public function discard(): static
    {
        return $this->addOption('-discard');
    }

    /**
     * Disposition.
     *
     * @return static
     */
    public function disposition(): static
    {
        return $this->addOption('-disposition');
    }

    /**
     * Accurate seek.
     *
     * @return static
     */
    public function accurateSeek(): static
    {
        return $this->addOption('-accurate_seek');
    }

    /**
     * Shortest output.
     *
     * @return static
     */
    public function shortest(): static
    {
        return $this->addOption('-shortest');
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
        return $this->addOption('-profile', $profile);
    }

    /**
     * Attach file as a stream.
     *
     * @param string $filename
     *
     * @return static
     *
     * @throws LogicException
     */
    public function attach(string $filename): static
    {
        if ($this->isInput) {
            throw new LogicException('Attach option can be used only for output files');
        }

        return $this->addOption('-attach', $filename);
    }

    /**
     * Move header to the start of the file.
     *
     * @return static
     *
     * @throws LogicException
     */
    public function moveHeaderToStart(): static
    {
        if ($this->isInput) {
            throw new LogicException('Movflags option can be used only for output files');
        }

        return $this->addOption('-movflags', 'faststart');
    }

    /**
     * Force loop over input file sequence.
     *
     * Option: -loop 1
     *
     * @param bool $flag
     *
     * @return static
     */
    public function loop(bool $flag = true): static
    {
        return $this->addOption('-loop', $flag ? '1' : '0');
    }

    /**
     * Add video stream.
     *
     * @param VideoStreamInterface|null $mapVideoStream
     *
     * @return VideoStreamInterface
     *
     * @throws LogicException
     */
    public function addVideoStream(?VideoStreamInterface $mapVideoStream = null): VideoStreamInterface
    {
        if ($this->isInput) {
            throw new LogicException('AddVideoStream can be used only for output files, use getVideoStream for input');
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
     * @param AudioStreamInterface|null $mapAudioStream
     *
     * @return AudioStreamInterface
     *
     * @throws LogicException
     */
    public function addAudioStream(?AudioStreamInterface $mapAudioStream = null): AudioStreamInterface
    {
        if ($this->isInput) {
            throw new LogicException('AddAudioStream can be used only for output files, use getAudioStream for input');
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
     * @param int $number
     *
     * @return VideoStreamInterface
     */
    public function getVideoStream(int $number = 0): VideoStreamInterface
    {
        if (!isset($this->videoStreams[$number])) {
            $this->videoStreams[$number] = new VideoStream($this, $number, $this->isInput);
        }

        return $this->videoStreams[$number];
    }

    /**
     * Get audio stream by number.
     *
     * @param int $number
     *
     * @return AudioStreamInterface
     */
    public function getAudioStream(int $number = 0): AudioStreamInterface
    {
        if (!isset($this->audioStreams[$number])) {
            $this->audioStreams[$number] = new AudioStream($this, $number, $this->isInput);
        }

        return $this->audioStreams[$number];
    }

    /**
     * Create a video stream.
     *
     * @return VideoStreamInterface
     *
     * @throws LogicException
     */
    public function createVideoStream(): VideoStreamInterface
    {
        if ($this->isInput) {
            throw new LogicException('CreateVideoStream can only be used for output files');
        }

        $streamName = sprintf('v%s_%d', $this->getName(), $this->streamsCounter++);

        return new VideoStream($this, $streamName, false, false);
    }

    /**
     * Create an audio stream.
     *
     * @return AudioStreamInterface
     *
     * @throws LogicException
     */
    public function createAudioStream(): AudioStreamInterface
    {
        if ($this->isInput) {
            throw new LogicException('CreateAudioStream can only be used for output files');
        }

        $streamName = sprintf('a%s_%d', $this->getName(), $this->streamsCounter++);

        return new AudioStream($this, $streamName, false, false);
    }

    /**
     * Create a stream with the given type.
     *
     * @param string $type
     *
     * @return StreamInterface
     *
     * @throws LogicException
     */
    public function createStream(string $type): StreamInterface
    {
        return match ($type) {
            StreamInterface::TYPE_VIDEO => $this->createVideoStream(),
            StreamInterface::TYPE_AUDIO => $this->createAudioStream(),
            default => throw new LogicException(sprintf('Unknown stream type: %s', $type)),
        };
    }

    /**
     * Returns number of the stream in the file.
     *
     * @param StreamInterface $stream
     *
     * @return int|null
     *
     * @throws LogicException
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
                throw new LogicException(sprintf('Unknown stream type: %s', $stream->getType()));
        }

        return $streamNumber === false ? null : $streamNumber;
    }

    /**
     * Move stream to the given position (stream index) in the output file.
     *
     * @param StreamInterface $stream
     * @param int             $position
     *
     * @return static
     *
     * @throws LogicException
     */
    public function moveStreamToPosition(StreamInterface $stream, int $position): static
    {
        if ($stream->getInput()) {
            throw new LogicException('You cannot reorder streams in the input file');
        }

        $streamNumber = $this->getStreamNumber($stream);

        if ($streamNumber === null) {
            throw new LogicException(sprintf('Stream %s not found', $stream->getName()));
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
                throw new LogicException(sprintf('Unknown stream type: %s', $stream->getType()));
        }

        return $this;
    }

    /**
     * Remove stream.
     *
     * @param StreamInterface $stream
     *
     * @return static
     */
    public function removeStream(StreamInterface $stream): static
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
        if ($this->filterGraph === null) {
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
