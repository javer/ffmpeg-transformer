<?php

namespace Javer\FfmpegTransformer\Tests\Profile;

use Javer\FfmpegTransformer\Profile\MediaProfile;
use PHPUnit\Framework\TestCase;

class MediaProfileTest extends TestCase
{
    /**
     * Test fromArray and toArray.
     */
    public function testFromAndToArray(): void
    {
        $referenceMediaProfile = [
            'name' => 'reference',
            'format' => 'mp4',
            'video' => [
                'width' => 1920,
                'height' => 1080,
                'codec' => 'h264',
                'profile' => 'main',
                'preset' => 'veryfast',
                'pixel_format' => 'yuv420p',
                'bitrate' => '6000k',
                'frame_rate' => 29.97,
                'keyframe_interval' => 250,
            ],
            'audio' => [
                'codec' => 'aac',
                'bitrate' => '128k',
                'sample_rate' => '48k',
            ],
        ];

        $mediaProfile = MediaProfile::fromArray($referenceMediaProfile);

        $serializedProfile = [
            'name' => 'reference',
            'format' => 'mp4',
            'video' => [
                [
                    'width' => 1920,
                    'height' => 1080,
                    'codec' => 'h264',
                    'profile' => 'main',
                    'preset' => 'veryfast',
                    'pixel_format' => 'yuv420p',
                    'bitrate' => '6000000',
                    'frame_rate' => 29.97,
                    'keyframe_interval' => 250,
                ],
            ],
            'audio' => [
                [
                    'codec' => 'aac',
                    'bitrate' => '128000',
                    'sample_rate' => '48000',
                ],
            ],
        ];

        self::assertEquals($serializedProfile, $mediaProfile->toArray());
    }
}
