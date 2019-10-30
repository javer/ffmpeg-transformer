<?php

namespace Javer\FfmpegTransformer\Transformer;

use Javer\FfmpegTransformer\Command\Command;
use Javer\FfmpegTransformer\File\FileInterface;
use Javer\FfmpegTransformer\Filter\Chain\ComplexFilterChainInterface;
use Javer\FfmpegTransformer\Profile\AudioProfile;
use Javer\FfmpegTransformer\Profile\MediaProfile;
use Javer\FfmpegTransformer\Profile\VideoProfile;
use Javer\FfmpegTransformer\Stream\AudioStreamInterface;
use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

/**
 * Class CommandTransformer
 *
 * @package Javer\FfmpegTransformer\Transformer
 */
class CommandTransformer
{
    /**
     * Map between codec name and corresponding ffmpeg encoder name.
     */
    protected const CODEC_MAP = [
        'h264' => 'libx264',
        'h265' => 'libx265',
    ];

    /**
     * Returns ffmpeg command to apply transformation to the input file.
     *
     * @param MediaProfile $transformation
     * @param string       $inputFilename
     * @param string       $outputFilename
     *
     * @return Command
     */
    public function applyTransformation(
        MediaProfile $transformation,
        string $inputFilename,
        string $outputFilename
    ): Command
    {
        $command = new Command();
        $command->overwriteOutputFiles();

        $inputFile = $command->addInput($inputFilename);

        $outputFile = $command->addOutput($outputFilename)
            ->moveHeaderToStart();

        $this->applyTransformationToFiles($transformation, $inputFile, $outputFile);

        return $command;
    }

    /**
     * Apply transformation to the given inputFile and outputFile.
     *
     * @param MediaProfile  $transformation
     * @param FileInterface $inputFile
     * @param FileInterface $outputFile
     */
    public function applyTransformationToFiles(
        MediaProfile $transformation,
        FileInterface $inputFile,
        FileInterface $outputFile
    ): void
    {
        foreach ($transformation->getVideoProfiles() as $trackNum => $videoProfile) {
            $inputVideoStream = $inputFile->getVideoStream($trackNum);

            $outputVideoStream = $outputFile->addVideoStream($inputVideoStream);

            $this->applyVideoTransformation($outputVideoStream, $videoProfile);
        }

        foreach ($transformation->getAudioProfiles() as $trackNum => $audioProfile) {
            $inputAudioStream = $inputFile->getAudioStream($trackNum);

            $outputAudioStream = $outputFile->addAudioStream($inputAudioStream);

            $this->applyAudioTransformation($outputAudioStream, $audioProfile);
        }
    }

    /**
     * Apply transformation to outputs of the given complex filter chain.
     *
     * @param MediaProfile                $transformation
     * @param ComplexFilterChainInterface $complexFilterChain
     * @param FileInterface               $outputFile
     */
    public function applyTransformationToComplexFilterChain(
        MediaProfile $transformation,
        ComplexFilterChainInterface $complexFilterChain,
        FileInterface $outputFile
    ): void
    {
        foreach ($complexFilterChain->getOutputVideoStreams() as $videoStream) {
            $outputVideoStream = $outputFile->addVideoStream($videoStream);

            $this->applyVideoTransformation($outputVideoStream, $transformation->getVideoProfile());
        }

        foreach ($complexFilterChain->getOutputAudioStreams() as $audioStream) {
            $outputAudioStream = $outputFile->addAudioStream($audioStream);

            $this->applyAudioTransformation($outputAudioStream, $transformation->getAudioProfile());
        }
    }

    /**
     * Apply video transformation to the given video stream.
     *
     * @param VideoStreamInterface $videoStream
     * @param VideoProfile         $videoProfile
     */
    public function applyVideoTransformation(VideoStreamInterface $videoStream, VideoProfile $videoProfile): void
    {
        if ($videoProfile->getWidth() > 0 || $videoProfile->getHeight() > 0) {
            $videoStream->frameSize(sprintf('%dx%d', $videoProfile->getWidth(), $videoProfile->getHeight()));
        }

        if (!is_null($codec = $videoProfile->getCodec())) {
            $videoStream->codec($this->getMappedCodec($codec));
        }

        if (!is_null($profile = $videoProfile->getProfile())) {
            $videoStream->profile($profile);
        }

        if (!is_null($preset = $videoProfile->getPreset())) {
            $videoStream->preset($preset);
        }

        if (!is_null($pixelFormat = $videoProfile->getPixelFormat())) {
            $videoStream->pixelFormat($pixelFormat);
        }

        if (!is_null($bitrate = $videoProfile->getBitrate())) {
            $videoStream->bitrate($bitrate);
        }

        if (!is_null($maxBitrate = $videoProfile->getMaxBitrate())) {
            $videoStream->maxBitrate($maxBitrate);
        }

        if (!is_null($minBitrate = $videoProfile->getMinBitrate())) {
            $videoStream->minBitrate($minBitrate);
        }

        if (!is_null($bufferSize = $videoProfile->getBufferSize())) {
            $videoStream->bufferSize($bufferSize);
        }

        if (!is_null($crf = $videoProfile->getCrf())) {
            $videoStream->crf($crf);
        }

        if (!is_null($frameRate = $videoProfile->getFrameRate())) {
            $videoStream->frameRate($frameRate);
        }

        if (!is_null($keyframeInterval = $videoProfile->getKeyframeInterval())) {
            $videoStream->keyframeInterval($keyframeInterval);
        }
    }

    /**
     * Apply audio transformation to the given audio stream.
     *
     * @param AudioStreamInterface $outputAudioStream
     * @param AudioProfile         $audioProfile
     */
    public function applyAudioTransformation(AudioStreamInterface $outputAudioStream, AudioProfile $audioProfile): void
    {
        if (!is_null($codec = $audioProfile->getCodec())) {
            $outputAudioStream->codec($this->getMappedCodec($codec));
        }

        if (!is_null($profile = $audioProfile->getProfile())) {
            $outputAudioStream->profile($profile);
        }

        if (!is_null($bitrate = $audioProfile->getBitrate())) {
            $outputAudioStream->bitrate($bitrate);
        }

        if (!is_null($sampleRate = $audioProfile->getSampleRate())) {
            $outputAudioStream->rate($sampleRate);
        }

        if (!is_null($channels = $audioProfile->getChannels())) {
            $outputAudioStream->channels($channels);
        }
    }

    /**
     * Returns mapped codec.
     *
     * @param string $codec
     *
     * @return string
     */
    protected function getMappedCodec(string $codec): string
    {
        return static::CODEC_MAP[$codec] ?? $codec;
    }
}
