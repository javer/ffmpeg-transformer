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
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const CODEC = 'codec';
    const PROFILE = 'profile';
    const PRESET = 'preset';
    const PIXEL_FORMAT = 'pixel_format';
    const BITRATE = 'bitrate';
    const MAX_BITRATE = 'max_bitrate';
    const MIN_BITRATE = 'min_bitrate';
    const BUFFER_SIZE = 'buffer_size';
    const CRF = 'crf';
    const FRAME_RATE = 'frame_rate';
    const KEYFRAME_INTERVAL = 'keyframe_interval';
    const ROTATE = 'rotate';

    /**
     * @var integer|null
     */
    private $width;

    /**
     * @var integer|null
     */
    private $height;

    /**
     * @var string|null
     */
    private $codec;

    /**
     * @var string|null
     */
    private $profile;

    /**
     * @var string|null
     */
    private $preset;

    /**
     * @var string|null
     */
    private $pixelFormat;

    /**
     * @var integer|null
     */
    private $bitrate;

    /**
     * @var integer|null
     */
    private $maxBitrate;

    /**
     * @var integer|null
     */
    private $minBitrate;

    /**
     * @var integer|null
     */
    private $bufferSize;

    /**
     * @var integer|null
     */
    private $crf;

    /**
     * @var float|null
     */
    private $frameRate;

    /**
     * @var integer|null
     */
    private $keyframeInterval;

    /**
     * @var integer|null
     */
    private $rotate;

    /**
     * Create a new profile from the given array of values.
     *
     * @param array $values
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
     * @return array
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

        $values = array_filter($values, function ($value): bool {
            return !is_null($value);
        });

        return $values;
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
     * @return string|integer|null
     */
    protected static function getMetadataValue(AbstractData $data, string $propertyName, $defaultValue = null)
    {
        $value = $data->has($propertyName) ? $data->get($propertyName) : $defaultValue;

        if ($value === 'unknown') {
            return null;
        } elseif (is_int($value)) {
            return (int) $value;
        } elseif (is_float($value)) {
            return (float) $value;
        } else {
            return $value;
        }
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

        if (strpos($rFrameRate, '/') !== false) {
            [$numerator, $denominator] = explode('/', $rFrameRate, 2);

            $rFrameRate = $denominator > 0 ? $numerator / $denominator : 0;
        }

        return (float) round($rFrameRate, 2);
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
    public function setWidth(int $width = null): VideoProfile
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
    public function setHeight(int $height = null): VideoProfile
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
    public function setCodec(string $codec = null): VideoProfile
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
    public function setProfile(string $profile = null): VideoProfile
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
    public function setPreset(string $preset = null): VideoProfile
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
    public function setPixelFormat(string $pixelFormat = null): VideoProfile
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
    public function setBitrate($bitrate = null): VideoProfile
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
    public function setMaxBitrate($maxBitrate = null): VideoProfile
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
    public function setMinBitrate($minBitrate = null): VideoProfile
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
    public function setBufferSize($bufferSize = null): VideoProfile
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
    public function setCrf(int $crf = null): VideoProfile
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
    public function setFrameRate(float $frameRate = null): VideoProfile
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
    public function setKeyframeInterval(int $keyframeInterval = null): VideoProfile
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
    public function setRotate(int $rotate = null): VideoProfile
    {
        $this->rotate = $rotate;

        return $this;
    }
}
