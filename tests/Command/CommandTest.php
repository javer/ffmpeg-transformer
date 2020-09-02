<?php

namespace Javer\FfmpegTransformer\Tests\Command;

use Javer\FfmpegTransformer\Command\Command;
use PHPUnit\Framework\TestCase;

/**
 * Class CommandTest
 *
 * @package Javer\FfmpegTransformer\Tests\Command
 */
class CommandTest extends TestCase
{
    /**
     * Test copying single video and audio stream to the output file in another format.
     */
    public function testFormatChange(): void
    {
        $command = (new Command())
            ->overwriteOutputFiles();

        $inputFile = $command->addInput('input.mov');

        $command->addOutput('output.mp4')
            ->moveHeaderToStart()
            ->addVideoStream($inputFile->getVideoStream())->end()
            ->addAudioStream($inputFile->getAudioStream())->end();

        self::assertEquals([
            '-y',
            '-i', 'input.mov',
            '-movflags', 'faststart',
            '-map', '0:v:0', '-c:v:0', 'copy',
            '-map', '0:a:0', '-c:a:0', 'copy',
            'output.mp4',
        ], $command->build());
    }

    /**
     * Test transcoding of the single video aud audio streams.
     */
    public function testTranscodingSingleVideoAndAudio(): void
    {
        $command = (new Command())
            ->overwriteOutputFiles();

        $inputFile = $command->addInput('input.mov');

        $command->addOutput('output.mp4')
            ->moveHeaderToStart()
            ->addVideoStream($inputFile->getVideoStream())
                ->codec('libx264')
                ->preset('veryfast')
                ->pixelFormat('yuv420p')
            ->end()
            ->addAudioStream($inputFile->getAudioStream())
                ->codec('aac')
            ->end();

        self::assertEquals([
            '-y',
            '-i', 'input.mov',
            '-movflags', 'faststart',
            '-map', '0:v:0', '-c:v:0', 'libx264', '-preset', 'veryfast', '-pix_fmt:v:0', 'yuv420p',
            '-map', '0:a:0', '-c:a:0', 'aac',
            'output.mp4',
        ], $command->build());
    }

    /**
     * Test audio splitting of the single audio track to several audio tracks.
     */
    public function testAudioSplitting(): void
    {
        $command = (new Command())
            ->overwriteOutputFiles();

        $inputFile = $command->addInput('input.mov');

        $command->addOutput('output.mp4')
            ->moveHeaderToStart()
            ->addVideoStream($inputFile->getVideoStream())->end()
            ->addAudioStream($inputFile->getAudioStream())
                ->filter('channelsplit=channel_layout=6')
                ->codec('aac')
                ->bitrate('64k')
            ->end();

        self::assertEquals([
            '-y',
            '-i', 'input.mov',
            '-movflags', 'faststart',
            '-map', '0:v:0', '-c:v:0', 'copy',
            '-map', '0:a:0', '-af', 'channelsplit=channel_layout=6', '-c:a:0', 'aac', '-b:a:0', '64k',
            'output.mp4',
        ], $command->build());
    }

    /**
     * Test VideoTrimConsumer::trimVideo() fast path.
     */
    public function testVideoTrim(): void
    {
        $command = (new Command())
            ->overwriteOutputFiles();

        $inputFile = $command->addInput('input.mov');

        $command->addOutput('output.mp4')
            ->moveHeaderToStart()
            ->startTime(10)
            ->duration(20)
            ->addVideoStream($inputFile->getVideoStream())->end()
            ->addAudioStream($inputFile->getAudioStream())->end();

        self::assertEquals([
            '-y',
            '-i', 'input.mov',
            '-movflags', 'faststart',
            '-ss', '10', '-t', '20',
            '-map', '0:v:0', '-c:v:0', 'copy',
            '-map', '0:a:0', '-c:a:0', 'copy',
            'output.mp4',
        ], $command->build());
    }

    /**
     * Test extract audio tracks.
     */
    public function testExtractAudioTracks(): void
    {
        $command = (new Command())
            ->overwriteOutputFiles();

        $inputAudioStream = $command->addInput('input.mov')
            ->getAudioStream(2);

        $blackVideoStream = $command->addInput('/dev/zero')
            ->format('rawvideo')
            ->getVideoStream()
                ->frameSize('16x16')
                ->pixelFormat('rgb24')
                ->frameRate(1);

        $command->addOutput('output.mp4')
            ->shortest()
            ->moveHeaderToStart()
            ->addAudioStream($inputAudioStream)->end()
            ->addVideoStream($blackVideoStream)
                ->pixelFormat('yuv420p')
                ->keyframeInterval(1)
                ->codec('libx264')
                ->preset('ultrafast');

        self::assertEquals([
            '-y',
            '-i', 'input.mov',
            '-f', 'rawvideo',  '-s:v:0', '16x16',  '-pix_fmt:v:0', 'rgb24',  '-r:v:0', '1',  '-i', '/dev/zero',
            '-shortest',
            '-movflags', 'faststart',
            '-map', '1:v:0', '-pix_fmt:v:0', 'yuv420p', '-g', '1', '-c:v:0', 'libx264', '-preset', 'ultrafast',
            '-map', '0:a:2', '-c:a:0', 'copy',
            'output.mp4',
        ], $command->build());
    }

    /**
     * Test filter graph video and audio filters.
     */
    public function testFilterGraphVideoAudio(): void
    {
        $command = (new Command())
            ->overwriteOutputFiles();

        $inputFile = $command->addInput('input.mp4');
        $inputVideo = $inputFile->getVideoStream();
        $inputAudio = $inputFile->getAudioStream();

        $outputFile = $command->addOutput('output.mp4')
            ->moveHeaderToStart();

        $filterGraph = $outputFile->filter();

        $outputVideoStream = $filterGraph->video([$inputVideo])
            ->trim(10, 20)
            ->trim(20, 30)
            ->trim(50, 60)
            ->resetTimestamp()
            ->getOutputStream();

        $outputAudioStream = $filterGraph->audio([$inputAudio])
            ->trim(10, 20)
            ->trim(20, 30)
            ->trim(50, 60)
            ->resetTimestamp()
            ->getOutputStream();

        $outputFile->addVideoStream($outputVideoStream)
            ->codec('libx264')
            ->preset('veryfast')
            ->pixelFormat('yuv420p');

        $outputFile->addAudioStream($outputAudioStream)
            ->codec('aac');

        self::assertEquals([
            '-y',
            '-i', 'input.mp4',
            '-movflags', 'faststart',
            '-filter_complex', '[0:v:0] trim=10:20, trim=20:30, trim=50:60, setpts=PTS-STARTPTS [v_0];'
            . ' [0:a:0] atrim=10:20, atrim=20:30, atrim=50:60, asetpts=PTS-STARTPTS [a_1]',
            '-map', '[v_0]', '-c:v:0', 'libx264', '-preset', 'veryfast', '-pix_fmt:v:0', 'yuv420p',
            '-map', '[a_1]', '-c:a:0', 'aac',
            'output.mp4',
        ], $command->build());
    }

    /**
     * Test filter graph pad left and right.
     */
    public function testFilterGraphPadLeftRight(): void
    {
        $command = (new Command())
            ->overwriteOutputFiles();

        $inputFile = $command->addInput('input.mp4');
        $inputVideoStream = $inputFile->getVideoStream();
        $inputAudioStream = $inputFile->getAudioStream();

        $outputFile = $command->addOutput('output.mp4')
            ->moveHeaderToStart();

        $filterGraph = $outputFile->filter();

        $duration = 10;
        $blackVideoStream = $command->generateBlackVideo(640, 480, $duration)->getVideoStream();
        $emptyAudioStream = $command->generateEmptyAudio($duration)->getAudioStream();
        [$outputVideoStream, $outputAudioStream] = $filterGraph
            ->complex([$blackVideoStream, $emptyAudioStream, $inputVideoStream, $inputAudioStream])
            ->concat()
            ->getOutputStreams();

        $duration = 20;
        $blackVideoStream = $command->generateBlackVideo(640, 480, $duration)->getVideoStream();
        $emptyAudioStream = $command->generateEmptyAudio($duration)->getAudioStream();
        [$outputVideoStream, $outputAudioStream] = $filterGraph
            ->complex([$outputVideoStream, $outputAudioStream, $blackVideoStream, $emptyAudioStream])
            ->concat()
            ->getOutputStreams();

        $outputFile->addVideoStream($outputVideoStream)
            ->codec('libx264')
            ->preset('veryfast')
            ->pixelFormat('yuv420p');

        $outputFile->addAudioStream($outputAudioStream)
            ->codec('aac');

        self::assertEquals([
            '-y',
            '-i', 'input.mp4',
            '-f', 'rawvideo', '-t', '10', '-s:v:0','640x480', '-pix_fmt:v:0', 'rgb24', '-i', '/dev/zero',
            '-f', 'lavfi', '-t', '10', '-i', 'aevalsrc=0',
            '-f', 'rawvideo', '-t', '20', '-s:v:0', '640x480', '-pix_fmt:v:0', 'rgb24', '-i', '/dev/zero',
            '-f', 'lavfi', '-t', '20', '-i', 'aevalsrc=0',
            '-movflags',  'faststart',
            '-filter_complex',
            '[1:v:0] [2:a:0] [0:v:0] [0:a:0] concat=n=2:v=1:a=1 [v_0] [a_1];'
            . ' [v_0] [a_1] [3:v:0] [4:a:0] concat=n=2:v=1:a=1 [v_2] [a_3]',
            '-map', '[v_2]', '-c:v:0', 'libx264', '-preset', 'veryfast', '-pix_fmt:v:0', 'yuv420p',
            '-map', '[a_3]', '-c:a:0', 'aac',
            'output.mp4',
        ], $command->build());
    }

    /**
     * Test filter graph sync ranges.
     */
    public function testFilterGraphSyncRanges(): void
    {
        $actions = [
            ['P', 5],
            ['T', 0, 5],
            ['T', 15, 20],
            ['P', 5],
            ['P', 5],
            ['T', 20, 30],
            ['P', 5],
            ['T', 35, 45],
            ['P', 3],
            ['T', 50, 54],
            ['P', 3],
        ];

        $command = (new Command())
            ->overwriteOutputFiles();

        $inputFile = $command->addInput('input.mp4');
        $inputVideoStream = $inputFile->getVideoStream();
        $inputAudioStream = $inputFile->getAudioStream();

        $outputFile = $command->addOutput('output.mp4')
            ->moveHeaderToStart();

        $filterGraph = $outputFile->filter();

        $streams = [];
        foreach ($actions as $actionData) {
            $action = $actionData[0];

            if ($action === 'P') {
                $duration = $actionData[1];
                $blackVideoStream = $command->generateBlackVideo(640, 480, $duration)->getVideoStream();
                $emptyAudioStream = $command->generateEmptyAudio($duration)->getAudioStream();
                $streams = array_merge($streams, [$blackVideoStream, $emptyAudioStream]);
            } elseif ($action === 'T') {
                $trimVideoStream = $filterGraph->video($inputVideoStream)
                    ->trim($actionData[1], $actionData[2])
                    ->resetTimestamp()
                    ->getOutputStream();
                $trimAudioStream = $filterGraph->audio($inputAudioStream)
                    ->trim($actionData[1], $actionData[2])
                    ->resetTimestamp()
                    ->getOutputStream();
                $streams = array_merge($streams, [$trimVideoStream, $trimAudioStream]);
            }
        }

        [$outputVideoStream, $outputAudioStream] = $filterGraph->complex($streams)
            ->concat()
            ->getOutputStreams();

        $outputFile->addVideoStream($outputVideoStream)
            ->codec('libx264')
            ->preset('veryfast')
            ->pixelFormat('yuv420p');

        $outputFile->addAudioStream($outputAudioStream)
            ->codec('aac');

        self::assertEquals([
            '-y',
            '-i', 'input.mp4',
            '-f', 'rawvideo', '-t', '5', '-s:v:0', '640x480', '-pix_fmt:v:0', 'rgb24', '-i', '/dev/zero',
            '-f', 'lavfi', '-t', '5', '-i', 'aevalsrc=0',
            '-f', 'rawvideo', '-t', '5', '-s:v:0', '640x480', '-pix_fmt:v:0', 'rgb24', '-i', '/dev/zero',
            '-f', 'lavfi', '-t', '5', '-i', 'aevalsrc=0',
            '-f', 'rawvideo', '-t', '5', '-s:v:0', '640x480', '-pix_fmt:v:0', 'rgb24', '-i', '/dev/zero',
            '-f', 'lavfi', '-t', '5', '-i', 'aevalsrc=0',
            '-f', 'rawvideo', '-t', '5', '-s:v:0', '640x480', '-pix_fmt:v:0', 'rgb24', '-i', '/dev/zero',
            '-f', 'lavfi', '-t', '5', '-i', 'aevalsrc=0',
            '-f', 'rawvideo', '-t', '3', '-s:v:0', '640x480', '-pix_fmt:v:0', 'rgb24', '-i', '/dev/zero',
            '-f', 'lavfi', '-t', '3', '-i', 'aevalsrc=0',
            '-f', 'rawvideo', '-t', '3', '-s:v:0', '640x480', '-pix_fmt:v:0', 'rgb24', '-i', '/dev/zero',
            '-f', 'lavfi', '-t', '3', '-i', 'aevalsrc=0',
            '-movflags', 'faststart',
            '-filter_complex',
            implode('; ', [
                '[0:v:0] trim=0:5, setpts=PTS-STARTPTS [v_0]', '[0:a:0] atrim=0:5, asetpts=PTS-STARTPTS [a_1]',
                '[0:v:0] trim=15:20, setpts=PTS-STARTPTS [v_2]', '[0:a:0] atrim=15:20, asetpts=PTS-STARTPTS [a_3]',
                '[0:v:0] trim=20:30, setpts=PTS-STARTPTS [v_4]', '[0:a:0] atrim=20:30, asetpts=PTS-STARTPTS [a_5]',
                '[0:v:0] trim=35:45, setpts=PTS-STARTPTS [v_6]', '[0:a:0] atrim=35:45, asetpts=PTS-STARTPTS [a_7]',
                '[0:v:0] trim=50:54, setpts=PTS-STARTPTS [v_8]', '[0:a:0] atrim=50:54, asetpts=PTS-STARTPTS [a_9]',
                '[1:v:0] [2:a:0] [v_0] [a_1] [v_2] [a_3] [3:v:0] [4:a:0] [5:v:0] [6:a:0] [v_4] [a_5] [7:v:0] [8:a:0]'
                . ' [v_6] [a_7] [9:v:0] [10:a:0] [v_8] [a_9] [11:v:0] [12:a:0] concat=n=11:v=1:a=1 [v_10] [a_11]',
            ]),
            '-map', '[v_10]', '-c:v:0', 'libx264', '-preset', 'veryfast', '-pix_fmt:v:0', 'yuv420p',
            '-map', '[a_11]', '-c:a:0', 'aac',
            'output.mp4',
        ], $command->build());
    }

    /**
     * Test mix and normalize audio tracks.
     */
    public function testMixAndNormalizeAudioTracks(): void
    {
        $inputFilename = 'input.mp4';
        $outputFilename = 'output.mp4';
        $codec = 'aac';

        $trackVolumes = [
            '-7.2',
            '-8.0',
            '-2.6',
            '-90.3',
            '-8.0',
            '-8.1'
        ];

        $channels = [
            0 => [],
            1 => [],
            2 => [],
            4 => [],
            5 => [],
        ];

        $command = new Command();
        $command->overwriteOutputFiles();

        $inputFile = $command->addInput($inputFilename);

        $outputFile = $command->addOutput($outputFilename)
            ->moveHeaderToStart();

        $outputFile->addVideoStream($inputFile->getVideoStream());

        $outputMixedAudioStream = $outputFile->addAudioStream();

        $mixAudioStreams = [];
        $filterGraph = $outputFile->filter();

        foreach ($trackVolumes as $trackNum => $trackVolume) {
            $audioStream = $inputFile->getAudioStream($trackNum);

            if (array_key_exists($trackNum, $channels)) {
                [$mixStream, $audioStream] = $filterGraph->audio($audioStream)
                    ->volume(0 - $trackVolume)
                    ->split(2)
                    ->getOutputStreams();

                $mixAudioStreams[] = $mixStream;

                $outputFile->addAudioStream($audioStream)
                    ->codec($codec);
            } else {
                $outputFile->addAudioStream($audioStream);
            }
        }

        $mixedAudioStream = $filterGraph->audio($mixAudioStreams)
            ->mix()
            ->volume(count($mixAudioStreams))
            ->getOutputStream();

        $outputMixedAudioStream->map($mixedAudioStream);
        $outputMixedAudioStream->codec($codec);

        self::assertEquals([
            '-y',
            '-i', 'input.mp4',
            '-movflags', 'faststart',
            '-filter_complex',
            implode('; ', [
                '[0:a:0] volume=7.200000dB, asplit=2 [a_0] [a_1]',
                '[0:a:1] volume=8.000000dB, asplit=2 [a_2] [a_3]',
                '[0:a:2] volume=2.600000dB, asplit=2 [a_4] [a_5]',
                '[0:a:4] volume=8.000000dB, asplit=2 [a_6] [a_7]',
                '[0:a:5] volume=8.100000dB, asplit=2 [a_8] [a_9]',
                '[a_0] [a_2] [a_4] [a_6] [a_8] amix=inputs=5, volume=5.000000dB [a_10]',
            ]),
            '-map', '0:v:0', '-c:v:0', 'copy',
            '-map', '[a_10]', '-c:a:0', 'aac',
            '-map', '[a_1]', '-c:a:1', 'aac',
            '-map', '[a_3]', '-c:a:2', 'aac',
            '-map', '[a_5]', '-c:a:3', 'aac',
            '-map', '0:a:3', '-c:a:4', 'copy',
            '-map', '[a_7]', '-c:a:5', 'aac',
            '-map', '[a_9]', '-c:a:6', 'aac',
            'output.mp4',
        ], $command->build());
    }

    /**
     * Test mix and normalize audio tracks with reordering output streams.
     */
    public function testMoveStreamToPosition(): void
    {
        $inputFilename = 'input.mp4';
        $outputFilename = 'output.mp4';
        $codec = 'aac';

        $trackVolumes = [
            '-7.2',
            '-8.0',
            '-2.6',
            '-90.3',
            '-8.0',
            '-8.1'
        ];

        $channels = [
            0 => [],
            1 => [],
            2 => [],
            4 => [],
            5 => [],
        ];

        $command = new Command();
        $command->overwriteOutputFiles();

        $inputFile = $command->addInput($inputFilename);

        $outputFile = $command->addOutput($outputFilename)
            ->moveHeaderToStart();

        $outputFile->addVideoStream($inputFile->getVideoStream());

        $mixAudioStreams = [];
        $filterGraph = $outputFile->filter();

        foreach ($trackVolumes as $trackNum => $trackVolume) {
            $audioStream = $inputFile->getAudioStream($trackNum);

            if (array_key_exists($trackNum, $channels)) {
                [$mixStream, $audioStream] = $filterGraph->audio($audioStream)
                    ->volume(0 - $trackVolume)
                    ->split(2)
                    ->getOutputStreams();

                $mixAudioStreams[] = $mixStream;

                $outputFile->addAudioStream($audioStream)
                    ->codec($codec);
            } else {
                $outputFile->addAudioStream($audioStream);
            }
        }

        $mixedAudioStream = $filterGraph->audio($mixAudioStreams)
            ->mix()
            ->volume(count($mixAudioStreams))
            ->getOutputStream();

        $outputMixedAudioStream = $outputFile->addAudioStream($mixedAudioStream);
        $outputMixedAudioStream->moveTo(0);
        $outputMixedAudioStream->codec($codec);

        self::assertEquals([
            '-y',
            '-i', 'input.mp4',
            '-movflags', 'faststart',
            '-filter_complex',
            implode('; ', [
                '[0:a:0] volume=7.200000dB, asplit=2 [a_0] [a_1]',
                '[0:a:1] volume=8.000000dB, asplit=2 [a_2] [a_3]',
                '[0:a:2] volume=2.600000dB, asplit=2 [a_4] [a_5]',
                '[0:a:4] volume=8.000000dB, asplit=2 [a_6] [a_7]',
                '[0:a:5] volume=8.100000dB, asplit=2 [a_8] [a_9]',
                '[a_0] [a_2] [a_4] [a_6] [a_8] amix=inputs=5, volume=5.000000dB [a_10]',
            ]),
            '-map', '0:v:0', '-c:v:0', 'copy',
            '-map', '[a_10]', '-c:a:0', 'aac',
            '-map', '[a_1]', '-c:a:1', 'aac',
            '-map', '[a_3]', '-c:a:2', 'aac',
            '-map', '[a_5]', '-c:a:3', 'aac',
            '-map', '0:a:3', '-c:a:4', 'copy',
            '-map', '[a_7]', '-c:a:5', 'aac',
            '-map', '[a_9]', '-c:a:6', 'aac',
            'output.mp4',
        ], $command->build());
    }
}
