<?php

namespace Javer\FfmpegTransformer\Profile;

use FFMpeg\Media\AbstractStreamableMedia;

/**
 * Class MediaProfile
 *
 * @package Javer\FfmpegTransformer\Profile
 */
class MediaProfile
{
    public const NAME = 'name';
    public const LABEL = 'label';
    public const FORMAT = 'format';
    public const DURATION = 'duration';
    public const SIZE = 'size';
    public const BITRATE = 'bitrate';
    public const VIDEO = 'video';
    public const AUDIO = 'audio';

    // Default supposed bitrate for audio track when it cannot be recognized from the input media file
    protected const DEFAULT_AUDIO_BITRATE = 64000;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $label;

    /**
     * @var string|null
     */
    private $format;

    /**
     * @var float|null
     */
    private $duration;

    /**
     * @var integer|null
     */
    private $size;

    /**
     * @var integer|null
     */
    private $bitrate;

    /**
     * @var VideoProfile[]
     */
    private $videoProfiles = [];

    /**
     * @var AudioProfile[]
     */
    private $audioProfiles = [];

    /**
     * Create a new profile from the given array of values.
     *
     * @param array $values
     *
     * @return MediaProfile
     */
    public static function fromArray(array $values): MediaProfile
    {
        $profile = new static();

        foreach ($values as $key => $value) {
            switch ($key) {
                case static::NAME:
                    $profile->setName($value);
                    break;

                case static::LABEL:
                    $profile->setLabel($value);
                    break;

                case static::FORMAT:
                    $profile->setFormat($value);
                    break;

                case static::DURATION:
                    $profile->setDuration($value);
                    break;

                case static::SIZE:
                    $profile->setSize($value);
                    break;

                case static::BITRATE:
                    $profile->setBitrate($value);
                    break;

                case static::VIDEO:
                    $profile->addVideoProfile(VideoProfile::fromArray($value));
                    break;

                case static::AUDIO:
                    $profile->addAudioProfile(AudioProfile::fromArray($value));
                    break;
            }
        }

        return $profile;
    }

    /**
     * Returns profile as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $values = [
            static::NAME => $this->getName(),
            static::LABEL => $this->getLabel(),
            static::FORMAT => $this->getFormat(),
            static::DURATION => $this->getDuration(),
            static::SIZE => $this->getSize(),
            static::BITRATE => $this->getBitrate(),
        ];

        foreach ($this->getVideoProfiles() as $videoProfile) {
            $values[static::VIDEO][] = $videoProfile->toArray();
        }

        foreach ($this->getAudioProfiles() as $audioProfile) {
            $values[static::AUDIO][] = $audioProfile->toArray();
        }

        $values = array_filter($values, static function ($value): bool {
            return $value !== null;
        });

        return $values;
    }

    /**
     * Create a new profile from the given media.
     *
     * @param AbstractStreamableMedia $media
     *
     * @return MediaProfile
     */
    public static function fromMedia(AbstractStreamableMedia $media): MediaProfile
    {
        $format = $media->getFormat();

        $profile = static::fromArray([
            static::FORMAT => trim(strrchr($format->get('filename'), '.'), '.'),
            static::DURATION => $format->get('duration'),
            static::SIZE => $format->get('size'),
            static::BITRATE => $format->get('bit_rate'),
        ]);

        $streams = $media->getStreams();

        foreach ($streams->videos() as $videoStream) {
            $profile->addVideoProfile(VideoProfile::fromMetadata($videoStream));
        }

        foreach ($streams->audios() as $audioStream) {
            $profile->addAudioProfile(AudioProfile::fromMetadata($audioStream));
        }

        $profile->repairProfileForMedia($media);

        return $profile;
    }

    /**
     * Repair profile for media.
     *
     * This method will guess life-critical metadata (size, duration, bitrates) for the given media
     * if it cannot be recognized from the file (for example, webm files do not contain such metadata values).
     *
     * @param AbstractStreamableMedia $media
     *
     * @return MediaProfile
     */
    public function repairProfileForMedia(AbstractStreamableMedia $media): MediaProfile
    {
        if (!$this->getSize() && file_exists($media->getPathfile())) {
            $this->setSize(filesize($media->getPathfile()));
        }

        if (!$this->getBitrate() && $this->getDuration() > 0) {
            $this->setBitrate((int) $this->getSize() / $this->getDuration() * 8);
        }

        $audioBitrate = 0;

        foreach ($this->getAudioProfiles() as $audioProfile) {
            if (!$audioProfile->getBitrate()) {
                $audioProfile->setBitrate(static::DEFAULT_AUDIO_BITRATE);
            }

            $audioBitrate += $audioProfile->getBitrate();
        }

        if (count($this->getVideoProfiles()) > 0) {
            $fixedBitrate = max(0, $this->getBitrate() - $audioBitrate) / count($this->getVideoProfiles());

            foreach ($this->getVideoProfiles() as $videoProfile) {
                if (!$videoProfile->getBitrate()) {
                    $videoProfile->setBitrate($fixedBitrate);
                }
            }
        }

        return $this;
    }

    /**
     * Returns name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return MediaProfile
     */
    public function setName(string $name = null): MediaProfile
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns label.
     *
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Set label.
     *
     * @param string|null $label
     *
     * @return MediaProfile
     */
    public function setLabel(string $label = null): MediaProfile
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns format.
     *
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * Set format.
     *
     * @param string|null $format
     *
     * @return MediaProfile
     */
    public function setFormat(string $format = null): MediaProfile
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Returns duration.
     *
     * @return float|null
     */
    public function getDuration(): ?float
    {
        return $this->duration;
    }

    /**
     * Set duration.
     *
     * @param float|null $duration
     *
     * @return MediaProfile
     */
    public function setDuration(float $duration = null): MediaProfile
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Returns size.
     *
     * @return integer|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * Set size.
     *
     * @param integer|null $size
     *
     * @return MediaProfile
     */
    public function setSize(int $size = null): MediaProfile
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Returns bitrate.
     *
     * @return integer|null
     */
    public function getBitrate(): ?int
    {
        return $this->bitrate;
    }

    /**
     * Set bitrate.
     *
     * @param integer|string|null $bitrate
     *
     * @return MediaProfile
     */
    public function setBitrate($bitrate = null): MediaProfile
    {
        $this->bitrate = static::convertMetricValue($bitrate);

        return $this;
    }

    /**
     * Returns video profiles.
     *
     * @return VideoProfile[]|null
     */
    public function getVideoProfiles(): array
    {
        return $this->videoProfiles;
    }

    /**
     * Returns first video profile.
     *
     * @return VideoProfile|null
     */
    public function getVideoProfile(): ?VideoProfile
    {
        return $this->videoProfiles[0] ?? null;
    }

    /**
     * Add video profile.
     *
     * @param VideoProfile $videoProfile
     *
     * @return MediaProfile
     */
    public function addVideoProfile(VideoProfile $videoProfile): MediaProfile
    {
        $this->videoProfiles[] = $videoProfile;

        return $this;
    }

    /**
     * Returns audio profiles.
     *
     * @return AudioProfile[]
     */
    public function getAudioProfiles(): array
    {
        return $this->audioProfiles;
    }

    /**
     * Returns first audio profile.
     *
     * @return AudioProfile|null
     */
    public function getAudioProfile(): ?AudioProfile
    {
        return $this->audioProfiles[0] ?? null;
    }

    /**
     * Add audio profile.
     *
     * @param AudioProfile $audioProfile
     *
     * @return MediaProfile
     */
    public function addAudioProfile(AudioProfile $audioProfile): MediaProfile
    {
        $this->audioProfiles[] = $audioProfile;

        return $this;
    }

    /**
     * Converts a metric value to the absolute value.
     *
     * @param string|integer|float|null $value
     *
     * @return integer|float|null
     */
    public static function convertMetricValue($value)
    {
        if (!is_string($value) || strlen($value) <= 1) {
            return $value;
        }

        $degree = strtoupper(substr($value, -1, 1));

        // Supported suffixes are: Kilo, Mega, Giga, Tera, Peta
        $exp = array_search($degree, ['K', 'M', 'G', 'T', 'P']);

        if ($exp !== false) {
            $value = ((int) substr($value, 0, -1)) * (1000 ** ($exp + 1));
        }

        return $value;
    }

    /**
     * Clones the current object.
     */
    public function __clone()
    {
        foreach ($this->videoProfiles as $key => $videoProfile) {
            $this->videoProfiles[$key] = clone $videoProfile;
        }

        foreach ($this->audioProfiles as $key => $audioProfile) {
            $this->audioProfiles[$key] = clone $audioProfile;
        }
    }
}
