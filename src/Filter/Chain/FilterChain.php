<?php

namespace Javer\FfmpegTransformer\Filter\Chain;

use Javer\FfmpegTransformer\Filter\Graph\FilterGraphInterface;
use Javer\FfmpegTransformer\Stream\StreamInterface;

/**
 * Class FilterChain
 *
 * @package Javer\FfmpegTransformer\Filter\Chain
 */
class FilterChain implements FilterChainInterface
{
    /**
     * @var FilterGraphInterface
     */
    protected $filterGraph;

    /**
     * @var StreamInterface[]
     */
    protected $inputStreams = [];

    /**
     * @var string[]
     */
    protected $filters = [];

    /**
     * @var StreamInterface[]
     */
    protected $outputStreams = [];

    /**
     * @var string[]
     */
    protected $outputs = [];

    /**
     * FilterChain constructor.
     *
     * @param FilterGraphInterface              $filterGraph
     * @param StreamInterface[]|StreamInterface $inputStreams
     */
    public function __construct(FilterGraphInterface $filterGraph, $inputStreams)
    {
        $this->filterGraph = $filterGraph;

        if (!is_array($inputStreams)) {
            $inputStreams = [$inputStreams];
        }

        foreach ($inputStreams as $inputStream) {
            $this->addInputStream($inputStream);
        }
    }

    /**
     * Build chain.
     *
     * @return string
     */
    public function build(): string
    {
        $inputs = [];
        foreach ($this->inputStreams as $inputStream) {
            $inputs[] = sprintf('[%s]', $inputStream->getName());
        }

        $outputs = [];
        foreach ($this->getOutputStreams() as $outputStream) {
            $outputs[] = sprintf('[%s]', $outputStream->getName());
        }

        return sprintf(
            '%s %s %s',
            implode(' ', $inputs),
            implode(', ', $this->filters),
            implode(' ', $outputs)
        );
    }

    /**
     * Returns a string representation of the chain.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->build();
    }

    /**
     * Clones the current chain.
     */
    public function __clone()
    {
        $this->inputStreams = [];
        $this->filters = [];
        $this->outputStreams = [];
    }

    /**
     * Add filter.
     *
     * @param string $name
     * @param array  $arguments
     * @param array  $inputs
     * @param array  $outputs
     *
     * @return FilterChainInterface
     *
     * @throws \LogicException
     */
    public function filter(string $name, array $arguments, array $inputs, array $outputs): FilterChainInterface
    {
        if (count($this->outputStreams) > 0) {
            throw new \LogicException('You cannot add filter after building chain');
        }

        if ($this->outputs != $inputs) {
            throw new \LogicException(sprintf(
                'Incompatible filter input, expected: "%s", actual: "%s"',
                implode(', ', $inputs),
                implode(', ', $this->outputs)
            ));
        }

        $filter = $name;

        if ($arguments) {
            $args = [];
            foreach ($arguments as $k => $v) {
                $args[] = is_integer($k) ? $v : sprintf('%s=%s', $k, $v);
            }
            $filter .= '=' . implode(':', $args);
        }

        $this->filters[] = $filter;
        $this->outputs = $outputs;

        return $this;
    }

    /**
     * Add input stream.
     *
     * @param StreamInterface $stream
     *
     * @return FilterChainInterface
     *
     * @throws \LogicException
     */
    public function addInputStream(StreamInterface $stream): FilterChainInterface
    {
        if (count($this->filters) > 0) {
            throw new \LogicException('Input streams can be added only before filters');
        }

        $this->inputStreams[] = $stream;

        $this->end()->end()->removeStream($stream);

        $this->outputs[] = $stream->getType();

        return $this;
    }

    /**
     * Get output stream.
     *
     * @param integer $number
     *
     * @return StreamInterface
     */
    public function getOutputStream(int $number = 0): StreamInterface
    {
        $outputStreams = $this->getOutputStreams();

        return $outputStreams[$number];
    }

    /**
     * Returns output streams.
     *
     * @param string $type
     *
     * @return StreamInterface[]
     */
    public function getOutputStreams(string $type = ''): array
    {
        if (count($this->outputStreams) == 0) {
            foreach ($this->outputs as $output) {
                $this->outputStreams[] = $this->end()->end()->createStream($output);
            }
        }

        if (empty($type)) {
            $outputStreams = $this->outputStreams;
        } else {
            $outputStreams = [];

            foreach ($this->outputStreams as $outputStream) {
                if ($outputStream->getType() === $type) {
                    $outputStreams[] = $outputStream;
                }
            }
        }

        return $outputStreams;
    }

    /**
     * Get outputs list by type.
     *
     * @param string $type
     *
     * @return string[]
     */
    protected function getOutputs(string $type = ''): array
    {
        if (empty($type)) {
            return $this->outputs;
        }

        $outputs = [];

        foreach ($this->outputs as $output) {
            if ($output === $type) {
                $outputs[] = $output;
            }
        }

        return $outputs;
    }

    /**
     * Return to FilterGraph.
     *
     * @return FilterGraphInterface
     */
    public function end(): FilterGraphInterface
    {
        return $this->filterGraph;
    }
}
