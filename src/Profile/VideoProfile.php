<?php

namespace Javer\FfmpegTransformer\Profile;

use FFMpeg\FFProbe\DataMapping\AbstractData;

/**
 * Class VideoProfile
 *
 * @package Javer\FfmpegTransformer\Profile
 */
class VideoProfile
{
    public const WIDTH = 'width';
    public const HEIGHT = 'height';
    public const CODEC = 'codec';
    public const PROFILE = 'profile';
    public const PRESET = 'preset';
    public const PIXEL_FORMAT = 'pixel_format';
    public const BITRATE = 'bitrate';
    public const MAX_BITRATE = 'max_bitrate';
    public const MIN_BITRATE = 'min_bitrate';
    public const BUFFER_SIZE = 'buffer_size';
    public const CRF = 'crf';
    public const FRAME_RATE = 'frame_rate';
    public const KEYFRAME_INTERVAL = 'keyframe_interval';
    public const ROTATE = 'rotate';

    private ?int $width = null;

    private ?int $height = null;

    private ?string $codec = null;

    private ?string $profile = null;

    private ?string $preset = null;

    private ?string $pixelFormat = null;

    private ?int $bitrate = null;

    private ?int $maxBitrate = null;

    private ?int $minBitrate = null;

    private ?int $bufferSize = null;

    private ?int $crf = null;

    private ?float $frameRate = null;

    private ?int $keyframeInterval = null;

    private ?int $rotate = null;

    final public function __construct()
    {
    }

    /**
     * Create a new profile from the given array of values.
     *
     * @param array<int|string, mixed> $values
     *
     * @return VideoProfile
     */
    public static function fromArray(array $values): VideoProfile
    {
        $profile = new static();

        foreach ($values as $key => $value) {
            switch ($key) {
                case static::WIDTH:
                    $profile->setWidth($value);
                    break;

                case static::HEIGHT:
                    $profile->setHeight($value);
                    break;

                case static::CODEC:
                    $profile->setCodec($value);
                    break;

                case static::PROFILE:
                    $profile->setProfile($value);
                    break;

                case static::PRESET:
                    $profile->setPreset($value);
                    break;

                case static::PIXEL_FORMAT:
                    $profile->setPixelFormat($value);
                    break;

                case static::BITRATE:
                    $profile->setBitrate($value);
                    break;

                case static::MAX_BITRATE:
                    $profile->setMaxBitrate($value);
                    break;

                case static::MIN_BITRATE:
                    $profile->setMinBitrate($value);
                    break;

                case static::BUFFER_SIZE:
                    $profile->setBufferSize($value);
                    break;

                case static::CRF:
                    $profile->setCrf($value);
                    break;

                case static::FRAME_RATE:
                    $profile->setFrameRate($value);
                    break;

                case static::KEYFRAME_INTERVAL:
                    $profile->setKeyframeInterval($value);
                    break;

                case static::ROTATE:
                    $profile->setRotate($value);
                    break;
            }
        }

        return $profile;
    }

    /**
     * Returns profile as an array.
     *
     * @return array<int|string, string|int|float>
     */
    public function toArray(): array
    {
        $values = [
            static::WIDTH => $this->getWidth(),
            static::HEIGHT => $this->getHeight(),
            static::CODEC => $this->getCodec(),
            static::PROFILE => $this->getProfile(),
            static::PRESET => $this->getPreset(),
            static::PIXEL_FORMAT => $this->getPixelFormat(),
            static::BITRATE => $this->getBitrate(),
            static::MAX_BITRATE => $this->getMaxBitrate(),
            static::MIN_BITRATE => $this->getMinBitrate(),
            static::BUFFER_SIZE => $this->getBufferSize(),
            static::CRF => $this->getCrf(),
            static::FRAME_RATE => $this->getFrameRate(),
            static::KEYFRAME_INTERVAL => $this->getKeyframeInterval(),
            static::ROTATE => $this->getRotate(),
        ];

        return array_filter($values, static fn(mixed $value): bool => $value !== null);
    }

    /**
     * Create a new profile from the given ffprobe stream metadata.
     *
     * @param AbstractData $data
     *
     * @return VideoProfile
     */
    public static function fromMetadata(AbstractData $data): VideoProfile
    {
        return static::fromArray([
            static::WIDTH => static::getMetadataValue($data, 'width'),
            static::HEIGHT => static::getMetadataValue($data, 'height'),
            static::CODEC => static::getMetadataValue($data, 'codec_name'),
            static::PROFILE => static::getMetadataValue($data, 'profile'),
            static::PIXEL_FORMAT => static::getMetadataValue($data, 'pix_fmt'),
            static::BITRATE => static::getMetadataValue($data, 'bit_rate'),
            static::FRAME_RATE => static::getMetadataFrameRate($data),
            static::ROTATE => static::getMetadataRotate($data),
        ]);
    }

    /**
     * Returns ffprobe stream metadata value.
     *
     * @param AbstractData $data
     * @param string       $propertyName
     * @param mixed        $defaultValue
     *
     * @return string|integer|float|null
     */
    protected static function getMetadataValue(
        AbstractData $data,
        string $propertyName,
        mixed $defaultValue = null
    ): mixed
    {
        $value = $data->has($propertyName) ? $data->get($propertyName) : $defaultValue;

        if ($value === 'unknown') {
            return null;
        }

        return $value;
    }

    /**
     * Returns ffprobe stream metadata frame rate.
     *
     * @param AbstractData $data
     *
     * @return float
     */
    protected static function getMetadataFrameRate(AbstractData $data): float
    {
        $rFrameRate = static::getMetadataValue($data, 'r_frame_rate');

        // r_frame_rate could contain incorrect FPS for H.264 codec, try to use avg_frame_rate instead
        // See for details: https://ffmpeg.org/pipermail/libav-user/2013-May/004715.html
        // https://github.com/FFmpeg/FFmpeg/blob/b23d4e52fd24b4c6f240a22d8d92a70bbb16464d/libavformat/dump.c#L475
        $avgFrameRate = static::getMetadataValue($data, 'avg_frame_rate');
        if ((int) $avgFrameRate > 0) {
            $rFrameRate = $avgFrameRate;
        }

        if (str_contains((string) $rFrameRate, '/')) {
            [$numerator, $denominator] = explode('/', (string) $rFrameRate, 2);

            $rFrameRate = $denominator > 0 ? $numerator / $denominator : 0;
        }

        return round((float) $rFrameRate, 2);
    }

    /**
     * Returns ffprobe stream metadata rotate angle.
     *
     * @param AbstractData $data
     *
     * @return integer
     */
    protected static function getMetadataRotate(AbstractData $data): int
    {
        $rotateDegree = 0;

        if ($data->has('tags')) {
            $tags = $data->get('tags');
            if (isset($tags['rotate'])) {
                $rotateDegree = $tags['rotate'];
            }
        }

        if (!$rotateDegree && $data->has('side_data_list')) {
            $sideDataList = $data->get('side_data_list');

            foreach ($sideDataList as $sideData) {
                if (isset($sideData['rotation'])) {
                    $rotateDegree = -$sideData['rotation'];
                }
            }
        }

        return (int) $rotateDegree;
    }

    /**
     * Returns width.
     *
     * @return integer|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * Set width.
     *
     * @param integer|null $width
     *
     * @return VideoProfile
     */
    public function setWidth(?int $width): VideoProfile
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Returns height.
     *
     * @return integer|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * Set height.
     *
     * @param integer|null $height
     *
     * @return VideoProfile
     */
    public function setHeight(?int $height): VideoProfile
    {
        $this->height = $height;

        return $this;
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
     * @return VideoProfile
     */
    public function setCodec(?string $codec): VideoProfile
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
     * @return VideoProfile
     */
    public function setProfile(?string $profile): VideoProfile
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Returns preset.
     *
     * @return string|null
     */
    public function getPreset(): ?string
    {
        return $this->preset;
    }

    /**
     * Set preset.
     *
     * @param string|null $preset
     *
     * @return VideoProfile
     */
    public function setPreset(?string $preset): VideoProfile
    {
        $this->preset = $preset;

        return $this;
    }

    /**
     * Returns pixelFormat.
     *
     * @return string|null
     */
    public function getPixelFormat(): ?string
    {
        return $this->pixelFormat;
    }

    /**
     * Set pixelFormat.
     *
     * @param string|null $pixelFormat
     *
     * @return VideoProfile
     */
    public function setPixelFormat(?string $pixelFormat): VideoProfile
    {
        $this->pixelFormat = $pixelFormat;

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
     * @return VideoProfile
     */
    public function setBitrate(mixed $bitrate): VideoProfile
    {
        $this->bitrate = MediaProfile::convertMetricValue($bitrate);

        return $this;
    }

    /**
     * Returns max bitrate.
     *
     * @return integer|null
     */
    public function getMaxBitrate(): ?int
    {
        return $this->maxBitrate;
    }

    /**
     * Set max bitrate.
     *
     * @param integer|string|null $maxBitrate
     *
     * @return VideoProfile
     */
    public function setMaxBitrate(mixed $maxBitrate): VideoProfile
    {
        $this->maxBitrate = MediaProfile::convertMetricValue($maxBitrate);

        return $this;
    }

    /**
     * Returns min bitrate.
     *
     * @return integer|null
     */
    public function getMinBitrate(): ?int
    {
        return $this->minBitrate;
    }

    /**
     * Set min bitrate.
     *
     * @param integer|string|null $minBitrate
     *
     * @return VideoProfile
     */
    public function setMinBitrate(mixed $minBitrate): VideoProfile
    {
        $this->minBitrate = MediaProfile::convertMetricValue($minBitrate);

        return $this;
    }

    /**
     * Returns buffer size.
     *
     * @return integer|null
     */
    public function getBufferSize(): ?int
    {
        return $this->bufferSize;
    }

    /**
     * Set buffer size.
     *
     * @param integer|string|null $bufferSize
     *
     * @return VideoProfile
     */
    public function setBufferSize(mixed $bufferSize): VideoProfile
    {
        $this->bufferSize = MediaProfile::convertMetricValue($bufferSize);

        return $this;
    }

    /**
     * Returns crf.
     *
     * @return integer|null
     */
    public function getCrf(): ?int
    {
        return $this->crf;
    }

    /**
     * Set crf.
     *
     * @param integer|null $crf
     *
     * @return VideoProfile
     */
    public function setCrf(?int $crf): VideoProfile
    {
        $this->crf = $crf;

        return $this;
    }

    /**
     * Returns frame rate.
     *
     * @return float|null
     */
    public function getFrameRate(): ?float
    {
        return $this->frameRate;
    }

    /**
     * Set frame rate.
     *
     * @param float|null $frameRate
     *
     * @return VideoProfile
     */
    public function setFrameRate(?float $frameRate): VideoProfile
    {
        $this->frameRate = $frameRate;

        return $this;
    }

    /**
     * Returns keyframe interval.
     *
     * @return integer|null
     */
    public function getKeyframeInterval(): ?int
    {
        return $this->keyframeInterval;
    }

    /**
     * Set keyframe interval.
     *
     * @param integer|null $keyframeInterval
     *
     * @return VideoProfile
     */
    public function setKeyframeInterval(?int $keyframeInterval): VideoProfile
    {
        $this->keyframeInterval = $keyframeInterval;

        return $this;
    }

    /**
     * Returns rotate angle.
     *
     * @return integer|null
     */
    public function getRotate(): ?int
    {
        return $this->rotate;
    }

    /**
     * Set rotate.
     *
     * @param integer|null $rotate
     *
     * @return VideoProfile
     */
    public function setRotate(?int $rotate): VideoProfile
    {
        $this->rotate = $rotate;

        return $this;
    }
}
