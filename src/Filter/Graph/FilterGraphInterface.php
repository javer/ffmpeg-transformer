<?php

namespace Javer\FfmpegTransformer\Filter\Graph;

use Javer\FfmpegTransformer\BuilderInterface;
use Javer\FfmpegTransformer\File\FileInterface;
use Javer\FfmpegTransformer\Filter\Chain\AudioFilterChainInterface;
use Javer\FfmpegTransformer\Filter\Chain\ComplexFilterChainInterface;
use Javer\FfmpegTransformer\Filter\Chain\VideoFilterChainInterface;
use Javer\FfmpegTransformer\Stream\AudioStreamInterface;
use Javer\FfmpegTransformer\Stream\StreamInterface;
use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

interface FilterGraphInterface extends BuilderInterface
{
    /**
     * Add video filter chain.
     *
     * @param VideoStreamInterface[]|VideoStreamInterface $inputStreams
     *
     * @return VideoFilterChainInterface
     */
    public function video(array|VideoStreamInterface $inputStreams): VideoFilterChainInterface;

    /**
     * Add audio filter chain.
     *
     * @param AudioStreamInterface[]|AudioStreamInterface $inputStreams
     *
     * @return AudioFilterChainInterface
     */
    public function audio(array|AudioStreamInterface $inputStreams): AudioFilterChainInterface;

    /**
     * Add complex filter chain.
     *
     * @param StreamInterface[]|StreamInterface $inputStreams
     *
     * @return ComplexFilterChainInterface
     */
    public function complex(array|StreamInterface $inputStreams): ComplexFilterChainInterface;

    /**
     * Return to file.
     *
     * @return FileInterface
     */
    public function end(): FileInterface;
}
