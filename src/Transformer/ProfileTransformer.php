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
    const CODEC_COPY = 'copy';
    const CODEC_H264 = 'h264';

    const PRESET_ULTRAFAST = 'ultrafast';

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

        $targetVideoProfile = $target->getVideoProfile();
        foreach ($source->getVideoProfiles() as $sourceVideoProfile) {
            $transform->addVideoProfile($this->transformVideo($sourceVideoProfile, $targetVideoProfile, $forceVideo));
        }

        $targetAudioProfile = $target->getAudioProfile();
        foreach ($source->getAudioProfiles() as $sourceAudioProfile) {
            $transform->addAudioProfile($this->transformAudio($sourceAudioProfile, $targetAudioProfile, $forceAudio));
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

        $sourceWidth = $source->getWidth();
        $sourceHeight = $source->getHeight();
        $targetWidth = $this->getLeastValue($sourceWidth, $target->getWidth());
        $targetHeight = $this->getLeastValue($sourceHeight, $target->getHeight());
        $sizeAlign = 4;

        if ($targetWidth > 0 && $targetHeight > 0 && ($sourceWidth > $targetWidth || $sourceHeight > $targetHeight)) {
            $scale = $this->getScaleRate($sourceWidth, $targetWidth, $sourceHeight, $targetHeight);

            if ($scale > 1) {
                $targetWidth = $sourceWidth / $scale;
                $targetHeight = $sourceHeight / $scale;
            }
        }

        $sourceCodec = $source->getCodec();
        $sourcePixelFormat = $source->getPixelFormat();
        $sourceRotate = $source->getRotate();
        $sourceBitrate = $source->getBitrate();
        $sourceFrameRate = $source->getFrameRate();

        $targetWidth = $this->alignNumber($targetWidth, $sizeAlign);
        $targetHeight = $this->alignNumber($targetHeight, $sizeAlign);

        $targetCodec = $target->getCodec() ?? $sourceCodec;

        $targetPixelFormat = $target->getPixelFormat() ?? $sourcePixelFormat;

        $targetBitrate = $this->getLeastValue($sourceBitrate, $target->getBitrate());

        $targetMaxrate = $this->getLeastValue($sourceBitrate, $target->getMaxBitrate());

        $targetFramerate = $this->getLeastValue($sourceFrameRate, $target->getFrameRate());

        if ($force
            || $sourceCodec !== $targetCodec
            || $sourcePixelFormat !== $targetPixelFormat
            || $sourceRotate != 0
            || $sourceWidth > $targetWidth
            || $sourceHeight > $targetHeight
            || $sourceBitrate > $targetBitrate
            || $sourceBitrate > $targetMaxrate
            || $sourceFrameRate > $targetFramerate) {
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

        $targetCodec = $target->getCodec() ?? $source->getCodec();

        if ($source->getCodec() !== $targetCodec) {
            $targetBitrate = $target->getBitrate();
        } else {
            $targetBitrate = $this->getLeastValue($source->getBitrate(), $target->getBitrate());
        }

        $targetSampleRate = $this->getLeastValue($source->getSampleRate(), $target->getSampleRate());

        $targetChannels = $this->getLeastValue($source->getChannels(), $target->getChannels());

        if ($force
            || $source->getCodec() !== $targetCodec
            || $source->getBitrate() > $targetBitrate
            || $source->getSampleRate() > $targetSampleRate
            || $source->getChannels() > $targetChannels) {
            $transform->setCodec($targetCodec);
            $transform->setProfile($target->getProfile());
            $transform->setBitrate($targetBitrate);
            $transform->setSampleRate($targetSampleRate);

            if ($source->getChannels() !== $targetChannels) {
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
    protected function getScaleRate(int $videoWidth, int $widthThreshold, int $videoHeight, int $heightThreshold)
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
    protected function getLeastValue($value, $threshold)
    {
        if ($value > 0 && $threshold > 0) {
            return min($value, $threshold);
        } elseif ($value > 0) {
            return $value;
        } else {
            return $threshold;
        }
    }

    /**
     * Returns video codec preset.
     *
     * @param string        $codec
     * @param string|null   $preset
     * @param integer|float $frameRate
     *
     * @return string|null
     */
    protected function getVideoCodecPreset(string $codec, ?string $preset, $frameRate): ?string
    {
        $codecPreset = $preset;

        // Neither of the presets except "ultrafast" produces a valid video with FPS<18 which can be played in browsers
        if ($codec === static::CODEC_H264 && $frameRate > 0 && $frameRate < 18) {
            $codecPreset = static::PRESET_ULTRAFAST;
        }

        return $codecPreset;
    }
}
