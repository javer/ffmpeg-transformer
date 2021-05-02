<?php

namespace Javer\FfmpegTransformer\Transformer;

use Javer\FfmpegTransformer\Profile\AudioProfile;
use Javer\FfmpegTransformer\Profile\MediaProfile;
use Javer\FfmpegTransformer\Profile\VideoProfile;

/**
 * Class ProfileTransformer
 *
 * @package Javer\FfmpegTransformer\Transformer
 */
class ProfileTransformer
{
    public const CODEC_COPY = 'copy';
    public const CODEC_H264 = 'h264';

    public const PRESET_ULTRAFAST = 'ultrafast';

    /**
     * Transform media.
     *
     * @param MediaProfile $source
     * @param MediaProfile $target
     * @param boolean      $forceVideo
     * @param boolean      $forceAudio
     *
     * @return MediaProfile
     */
    public function transformMedia(
        MediaProfile $source,
        MediaProfile $target,
        bool $forceVideo = false,
        bool $forceAudio = false
    ): MediaProfile
    {
        $transform = new MediaProfile();

        if (!is_null($format = $target->getFormat()) && $source->getFormat() !== $format) {
            $transform->setFormat($format);
        }

        if (!is_null($duration = $target->getDuration()) && $source->getDuration() > $duration) {
            $transform->setDuration($duration);
        }

        if (!is_null($bitrate = $target->getBitrate()) && $source->getBitrate() > $bitrate) {
            $transform->setBitrate($bitrate);
        }

        if ($targetVideoProfile = $target->getVideoProfile()) {
            foreach ($source->getVideoProfiles() as $sourceVideoProfile) {
                $transform->addVideoProfile(
                    $this->transformVideo($sourceVideoProfile, $targetVideoProfile, $forceVideo)
                );
            }
        }

        if ($targetAudioProfile = $target->getAudioProfile()) {
            foreach ($source->getAudioProfiles() as $sourceAudioProfile) {
                $transform->addAudioProfile(
                    $this->transformAudio($sourceAudioProfile, $targetAudioProfile, $forceAudio)
                );
            }
        }

        return $transform;
    }

    /**
     * Transform video.
     *
     * @param VideoProfile $source
     * @param VideoProfile $target
     * @param boolean      $force
     *
     * @return VideoProfile
     */
    public function transformVideo(VideoProfile $source, VideoProfile $target, bool $force = false): VideoProfile
    {
        $transform = new VideoProfile();

        $sourceRotate = (int) $source->getRotate();

        // FFmpeg 4.0+ automatically rotates the source video
        if (in_array(abs($sourceRotate), [90, 270], true)) {
            $sourceWidth = (int) $source->getHeight();
            $sourceHeight = (int) $source->getWidth();
        } else {
            $sourceWidth = (int) $source->getWidth();
            $sourceHeight = (int) $source->getHeight();
        }

        $targetWidth = (int) $this->getLeastValue($sourceWidth, (int) $target->getWidth());
        $targetHeight = (int) $this->getLeastValue($sourceHeight, (int) $target->getHeight());
        $sizeAlign = 4;

        if ($targetWidth > 0 && $targetHeight > 0 && ($sourceWidth > $targetWidth || $sourceHeight > $targetHeight)) {
            $scale = $this->getScaleRate($sourceWidth, $targetWidth, $sourceHeight, $targetHeight);

            if ($scale > 1) {
                $targetWidth = (int) ($sourceWidth / $scale);
                $targetHeight = (int) ($sourceHeight / $scale);
            }
        }

        $sourceCodec = (string) $source->getCodec();
        $sourcePixelFormat = (string) $source->getPixelFormat();
        $sourceBitrate = (int) $source->getBitrate();
        $sourceFrameRate = (float) $source->getFrameRate();

        $targetWidth = $this->alignNumber($targetWidth, $sizeAlign);
        $targetHeight = $this->alignNumber($targetHeight, $sizeAlign);

        $targetCodec = $target->getCodec() ?? $sourceCodec;

        $targetPixelFormat = $target->getPixelFormat() ?? $sourcePixelFormat;

        $targetBitrate = (int) $this->getLeastValue($sourceBitrate, (int) $target->getBitrate());

        $targetMaxrate = (int) $this->getLeastValue($sourceBitrate, (int) $target->getMaxBitrate());

        $targetFramerate = (float) $this->getLeastValue($sourceFrameRate, (float) $target->getFrameRate());

        if (
            $force
            || $sourceCodec !== $targetCodec
            || $sourcePixelFormat !== $targetPixelFormat
            || $sourceRotate !== 0
            || $sourceWidth > $targetWidth
            || $sourceHeight > $targetHeight
            || $sourceBitrate > $targetBitrate
            || $sourceBitrate > $targetMaxrate
            || $sourceFrameRate > $targetFramerate
        ) {
            $transform->setCodec($targetCodec);
            $transform->setProfile($target->getProfile());
            $transform->setPreset($this->getVideoCodecPreset($targetCodec, $target->getPreset(), $targetFramerate));
            $transform->setPixelFormat($target->getPixelFormat());
            $transform->setBitrate($target->getBitrate() ? $targetBitrate : null);
            $transform->setMaxBitrate($target->getMaxBitrate());
            $transform->setMinBitrate($target->getMinBitrate());
            $transform->setBufferSize($target->getBufferSize());
            $transform->setCrf($target->getCrf());
            $transform->setFrameRate($targetFramerate);
            $transform->setKeyframeInterval($target->getKeyframeInterval());

            if ($sourceWidth !== $targetWidth || $sourceHeight !== $targetHeight) {
                $transform->setWidth($targetWidth);
                $transform->setHeight($targetHeight);
            }
        } else {
            $transform->setCodec(static::CODEC_COPY);
        }

        return $transform;
    }

    /**
     * Transform audio.
     *
     * @param AudioProfile $source
     * @param AudioProfile $target
     * @param boolean      $force
     *
     * @return AudioProfile
     */
    public function transformAudio(AudioProfile $source, AudioProfile $target, bool $force = false): AudioProfile
    {
        $transform = new AudioProfile();

        $sourceCodec = (string) $source->getCodec();
        $sourceBitrate = (int) $source->getBitrate();
        $sourceSampleRate = (int) $source->getSampleRate();
        $sourceChannels = (int) $source->getChannels();

        $targetCodec = $target->getCodec() ?? $sourceCodec;

        if ($sourceCodec !== $targetCodec) {
            $targetBitrate = (int) $target->getBitrate();
        } else {
            $targetBitrate = (int) $this->getLeastValue($sourceBitrate, (int) $target->getBitrate());
        }

        $targetSampleRate = (int) $this->getLeastValue($sourceSampleRate, (int) $target->getSampleRate());

        $targetChannels = (int) $this->getLeastValue($sourceChannels, (int) $target->getChannels());

        if (
            $force
            || $sourceCodec !== $targetCodec
            || $sourceBitrate > $targetBitrate
            || $sourceSampleRate > $targetSampleRate
            || $sourceChannels > $targetChannels
        ) {
            $transform->setCodec($targetCodec);
            $transform->setProfile($target->getProfile());
            $transform->setBitrate($targetBitrate);
            $transform->setSampleRate($targetSampleRate);

            if ($sourceChannels !== $targetChannels) {
                $transform->setChannels($targetChannels);
            }
        } else {
            $transform->setCodec(static::CODEC_COPY);
        }

        return $transform;
    }

    /**
     * Returns scale rate.
     *
     * @param integer $videoWidth
     * @param integer $widthThreshold
     * @param integer $videoHeight
     * @param integer $heightThreshold
     *
     * @return float
     */
    protected function getScaleRate(int $videoWidth, int $widthThreshold, int $videoHeight, int $heightThreshold): float
    {
        return max(1, max($videoWidth / $widthThreshold, $videoHeight / $heightThreshold));
    }

    /**
     * Returns aligned number.
     *
     * @param integer $number
     * @param integer $align
     *
     * @return integer
     */
    protected function alignNumber(int $number, int $align): int
    {
        return (int) round($number / $align, 0) * $align;
    }

    /**
     * Returns least non-zero value.
     *
     * @param integer|float $value
     * @param integer|float $threshold
     *
     * @return integer|float
     */
    protected function getLeastValue(mixed $value, mixed $threshold): mixed
    {
        if ($value > 0 && $threshold > 0) {
            return min($value, $threshold);
        }

        if ($value > 0) {
            return $value;
        }

        return $threshold;
    }

    /**
     * Returns video codec preset.
     *
     * @param string         $codec
     * @param string|null    $preset
     * @param int|float|null $frameRate
     *
     * @return string|null
     */
    protected function getVideoCodecPreset(string $codec, ?string $preset, int|float|null $frameRate): ?string
    {
        $codecPreset = $preset;

        // Neither of the presets except "ultrafast" produces a valid video with FPS<18 which can be played in browsers
        if ($codec === static::CODEC_H264 && $frameRate > 0 && $frameRate < 18) {
            $codecPreset = static::PRESET_ULTRAFAST;
        }

        return $codecPreset;
    }
}
