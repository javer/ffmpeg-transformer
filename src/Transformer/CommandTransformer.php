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
        if ($videoTransformation = $transformation->getVideoProfile()) {
            foreach ($complexFilterChain->getOutputVideoStreams() as $videoStream) {
                $outputVideoStream = $outputFile->addVideoStream($videoStream);

                $this->applyVideoTransformation($outputVideoStream, $videoTransformation);
            }
        }

        if ($audioTransformation = $transformation->getAudioProfile()) {
            foreach ($complexFilterChain->getOutputAudioStreams() as $audioStream) {
                $outputAudioStream = $outputFile->addAudioStream($audioStream);

                $this->applyAudioTransformation($outputAudioStream, $audioTransformation);
            }
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

        if (($codec = $videoProfile->getCodec()) !== null) {
            $videoStream->codec($this->getMappedCodec($codec));
        }

        if (($profile = $videoProfile->getProfile()) !== null) {
            $videoStream->profile($profile);
        }

        if (($preset = $videoProfile->getPreset()) !== null) {
            $videoStream->preset($preset);
        }

        if (($pixelFormat = $videoProfile->getPixelFormat()) !== null) {
            $videoStream->pixelFormat($pixelFormat);
        }

        if (($bitrate = $videoProfile->getBitrate()) !== null) {
            $videoStream->bitrate((string) $bitrate);
        }

        if (($maxBitrate = $videoProfile->getMaxBitrate()) !== null) {
            $videoStream->maxBitrate((string) $maxBitrate);
        }

        if (($minBitrate = $videoProfile->getMinBitrate()) !== null) {
            $videoStream->minBitrate((string) $minBitrate);
        }

        if (($bufferSize = $videoProfile->getBufferSize()) !== null) {
            $videoStream->bufferSize((string) $bufferSize);
        }

        if (($crf = $videoProfile->getCrf()) !== null) {
            $videoStream->crf($crf);
        }

        if (($frameRate = $videoProfile->getFrameRate()) !== null) {
            $videoStream->frameRate($frameRate);
        }

        if (($keyframeInterval = $videoProfile->getKeyframeInterval()) !== null) {
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
        if (($codec = $audioProfile->getCodec()) !== null) {
            $outputAudioStream->codec($this->getMappedCodec($codec));
        }

        if (($profile = $audioProfile->getProfile()) !== null) {
            $outputAudioStream->profile($profile);
        }

        if (($bitrate = $audioProfile->getBitrate()) !== null) {
            $outputAudioStream->bitrate((string) $bitrate);
        }

        if (($sampleRate = $audioProfile->getSampleRate()) !== null) {
            $outputAudioStream->rate($sampleRate);
        }

        if (($channels = $audioProfile->getChannels()) !== null) {
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
