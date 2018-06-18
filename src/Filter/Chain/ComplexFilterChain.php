<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use Javer\FfmpegTransformer\Stream\AudioStreamInterface;
use Javer\FfmpegTransformer\Stream\StreamInterface;
use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

/**
 * Class ComplexFilterChain
 *
 * @package Javer\FfmpegTransformer\Filter\Chain
 */
class ComplexFilterChain extends FilterChain implements ComplexFilterChainInterface
{
    /**
     * Concat filter.
     *
     * @return ComplexFilterChainInterface
     *
     * @throws \LogicException
     */
    public function concat(): ComplexFilterChainInterface
    {
        $videoCount = count($this->getOutputs(StreamInterface::TYPE_VIDEO));
        $audioCount = count($this->getOutputs(StreamInterface::TYPE_AUDIO));

        $partsCount = max($videoCount, $audioCount);
        if ($videoCount > 0 && $audioCount > 0 && $videoCount !== $audioCount) {
            $partsCount = min($videoCount, $audioCount);
        }

        if ($videoCount % $partsCount != 0 || $audioCount % $partsCount != 0) {
            throw new \LogicException('All segments must have the same number of streams of each type');
        }

        $videoCount /= $partsCount;
        $audioCount /= $partsCount;

        $inputs = [];

        for ($part = 0; $part < $partsCount; $part++) {
            $inputs = array_merge($inputs, array_fill(0, $videoCount, 'v'), array_fill(0, $audioCount, 'a'));
        }

        $outputs = array_merge(array_fill(0, $videoCount, 'v'), array_fill(0, $audioCount, 'a'));

        return $this->filter(
            'concat',
            ['n' => $partsCount, 'v' => $videoCount, 'a' => $audioCount],
            $inputs,
            $outputs
        );
    }

    /**
     * Get output video stream by number.
     *
     * @param integer $number
     *
     * @return VideoStreamInterface
     */
    public function getOutputVideoStream(int $number = 0): VideoStreamInterface
    {
        return $this->getOutputVideoStreams()[$number];
    }

    /**
     * Get output video streams.
     *
     * @return VideoStreamInterface[]
     */
    public function getOutputVideoStreams(): array
    {
        return $this->getOutputStreams(StreamInterface::TYPE_VIDEO);
    }

    /**
     * Get output audio stream by number.
     *
     * @param integer $number
     *
     * @return AudioStreamInterface
     */
    public function getOutputAudioStream(int $number = 0): AudioStreamInterface
    {
        return $this->getOutputAudioStreams()[$number];
    }

    /**
     * Get output audio streams.
     *
     * @return AudioStreamInterface[]
     */
    public function getOutputAudioStreams(): array
    {
        return $this->getOutputStreams(StreamInterface::TYPE_AUDIO);
    }
}
