<?php

namespace Javer\FfmpegTransformer\Filter\Graph;

use Javer\FfmpegTransformer\File\FileInterface;
use Javer\FfmpegTransformer\Filter\Chain\AudioFilterChain;
use Javer\FfmpegTransformer\Filter\Chain\AudioFilterChainInterface;
use Javer\FfmpegTransformer\Filter\Chain\ComplexFilterChain;
use Javer\FfmpegTransformer\Filter\Chain\ComplexFilterChainInterface;
use Javer\FfmpegTransformer\Filter\Chain\FilterChainInterface;
use Javer\FfmpegTransformer\Filter\Chain\VideoFilterChain;
use Javer\FfmpegTransformer\Filter\Chain\VideoFilterChainInterface;
use Javer\FfmpegTransformer\Stream\AudioStreamInterface;
use Javer\FfmpegTransformer\Stream\StreamInterface;
use Javer\FfmpegTransformer\Stream\VideoStreamInterface;

/**
 * Class FilterGraph
 *
 * @package Javer\FfmpegTransformer\Filter\Graph
 */
class FilterGraph implements FilterGraphInterface
{
    /**
     * @var FileInterface
     */
    protected $file;

    /**
     * @var FilterChainInterface[]
     */
    protected $filterChains = [];

    /**
     * FilterGraph constructor.
     *
     * @param FileInterface $file
     */
    public function __construct(FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * Build command.
     *
     * @return array
     */
    public function build(): array
    {
        $filterChains = [];

        foreach ($this->filterChains as $filterChain) {
            $filterChains[] = $filterChain->build();
        }

        return $filterChains ? ['-filter_complex', implode('; ', $filterChains)] : [];
    }

    /**
     * Returns a string representation of the filter graph.
     *
     * @return string
     */
    public function __toString(): string
    {
        return implode(' ', array_map('escapeshellarg', $this->build()));
    }

    /**
     * Clones the current filter graph.
     */
    public function __clone()
    {
        $this->filterChains = [];
    }

    /**
     * Add video filter chain.
     *
     * @param VideoStreamInterface[]|VideoStreamInterface $inputStreams
     *
     * @return VideoFilterChainInterface
     */
    public function video($inputStreams): VideoFilterChainInterface
    {
        $videoFilterChain = new VideoFilterChain($this, $inputStreams);

        $this->filterChains[] = $videoFilterChain;

        return $videoFilterChain;
    }

    /**
     * Add audio filter chain.
     *
     * @param AudioStreamInterface[]|AudioStreamInterface $inputStreams
     *
     * @return AudioFilterChainInterface
     */
    public function audio($inputStreams): AudioFilterChainInterface
    {
        $audioFilterChain = new AudioFilterChain($this, $inputStreams);

        $this->filterChains[] = $audioFilterChain;

        return $audioFilterChain;
    }

    /**
     * Add complex filter chain.
     *
     * @param StreamInterface[]|StreamInterface $inputStreams
     *
     * @return ComplexFilterChainInterface
     */
    public function complex($inputStreams): ComplexFilterChainInterface
    {
        $complexFilterChain = new ComplexFilterChain($this, $inputStreams);

        $this->filterChains[] = $complexFilterChain;

        return $complexFilterChain;
    }

    /**
     * Return to file.
     *
     * @return FileInterface
     */
    public function end(): FileInterface
    {
        return $this->file;
    }
}
