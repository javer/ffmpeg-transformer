<?php

namespace Javer\FfmpegTransformer\Profile;

use FFMpeg\FFProbe\DataMapping\AbstractData;

/**
 * Class AudioProfile
 *
 * @package Javer\FfmpegTransformer\Profile
 */
class AudioProfile
{
    public const CODEC = 'codec';
    public const PROFILE = 'profile';
    public const BITRATE = 'bitrate';
    public const SAMPLE_RATE = 'sample_rate';
    public const CHANNELS = 'channels';
    public const DURATION = 'duration';

    /**
     * @var string|null
     */
    private $codec;

    /**
     * @var string|null
     */
    private $profile;

    /**
     * @var integer|null
     */
    private $bitrate;

    /**
     * @var integer|null
     */
    private $sampleRate;

    /**
     * @var integer|null
     */
    private $channels;

    /**
     * @var float|null
     */
    private $duration;

    /**
     * Create a new profile from the given array of values.
     *
     * @param array $values
     *
     * @return AudioProfile
     */
    public static function fromArray(array $values): AudioProfile
    {
        $profile = new static();

        foreach ($values as $key => $value) {
            switch ($key) {
                case static::CODEC:
                    $profile->setCodec($value);
                    break;

                case static::PROFILE:
                    $profile->setProfile($value);
                    break;

                case static::BITRATE:
                    $profile->setBitrate($value);
                    break;

                case static::SAMPLE_RATE:
                    $profile->setSampleRate($value);
                    break;

                case static::CHANNELS:
                    $profile->setChannels($value);
                    break;

                case static::DURATION:
                    $profile->setDuration($value);
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
            static::CODEC => $this->getCodec(),
            static::PROFILE => $this->getProfile(),
            static::BITRATE => $this->getBitrate(),
            static::SAMPLE_RATE => $this->getSampleRate(),
            static::CHANNELS => $this->getChannels(),
            static::DURATION => $this->getDuration(),
        ];

        $values = array_filter($values, static function ($value): bool {
            return $value !== null;
        });

        return $values;
    }

    /**
     * Create a new profile from the given ffprobe stream metadata.
     *
     * @param AbstractData $data
     *
     * @return AudioProfile
     */
    public static function fromMetadata(AbstractData $data): AudioProfile
    {
        return static::fromArray([
            static::CODEC => static::getMetadataValue($data, 'codec_name'),
            static::PROFILE => static::getMetadataValue($data, 'profile'),
            static::BITRATE => static::getMetadataValue($data, 'bit_rate'),
            static::SAMPLE_RATE => static::getMetadataValue($data, 'sample_rate'),
            static::CHANNELS => static::getMetadataValue($data, 'channels'),
            static::DURATION => static::getMetadataValue($data, 'duration'),
        ]);
    }

    /**
     * Get ffprobe stream metadata value.
     *
     * @param AbstractData $data
     * @param string       $propertyName
     * @param mixed        $defaultValue
     *
     * @return string|integer|null
     */
    protected static function getMetadataValue(AbstractData $data, string $propertyName, $defaultValue = null)
    {
        $value = $data->has($propertyName) ? $data->get($propertyName) : $defaultValue;

        if ($value === 'unknown') {
            return null;
        }

        if (is_int($value)) {
            return (int) $value;
        }

        if (is_float($value)) {
            return (float) $value;
        }

        return $value;
    }

    /**
     * Returns codec.
     *
     * @return string|null
     */
    public function getCodec(): ?string
    {
        return $this->codec;
    }

    /**
     * Set codec.
     *
     * @param string|null $codec
     *
     * @return AudioProfile
     */
    public function setCodec(string $codec = null): AudioProfile
    {
        $this->codec = $codec;

        return $this;
    }

    /**
     * Returns profile.
     *
     * @return string|null
     */
    public function getProfile(): ?string
    {
        return $this->profile;
    }

    /**
     * Set profile.
     *
     * @param string|null $profile
     *
     * @return AudioProfile
     */
    public function setProfile(string $profile = null): AudioProfile
    {
        $this->profile = $profile;

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
     * @return AudioProfile
     */
    public function setBitrate($bitrate = null): AudioProfile
    {
        $this->bitrate = MediaProfile::convertMetricValue($bitrate);

        return $this;
    }

    /**
     * Returns sample rate.
     *
     * @return integer|null
     */
    public function getSampleRate(): ?int
    {
        return $this->sampleRate;
    }

    /**
     * Set sample rate.
     *
     * @param integer|string|null $sampleRate
     *
     * @return AudioProfile
     */
    public function setSampleRate($sampleRate = null): AudioProfile
    {
        $this->sampleRate = MediaProfile::convertMetricValue($sampleRate);

        return $this;
    }

    /**
     * Returns channels count.
     *
     * @return integer|null
     */
    public function getChannels(): ?int
    {
        return $this->channels;
    }

    /**
     * Set channels count.
     *
     * @param integer|null $channels
     *
     * @return AudioProfile
     */
    public function setChannels(int $channels = null): AudioProfile
    {
        $this->channels = $channels;

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
     * @return AudioProfile
     */
    public function setDuration(float $duration = null): AudioProfile
    {
        $this->duration = $duration;

        return $this;
    }
}
