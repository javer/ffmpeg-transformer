FFmpeg transformer
==================

This library simplifies usage of [FFmpeg](https://www.ffmpeg.org) for complex transcoding of the media files in PHP applications.

Features:
- FFmpeg command builder
- Media profile builder
- Profile transformer
- Command transformer

Requirements
------------

- PHP 7.1+
- [php-ffmpeg/php-ffmpeg](https://packagist.org/packages/php-ffmpeg/php-ffmpeg) for extracting media profile directly from the media file.

Installation
------------

Install the library using composer:
```sh
composer require javer/ffmpeg-transformer
```

FFmpeg command builder
----------------------

Enables you to build FFmpeg command line in OOP style.

For example, for retranscoding of the source media file in any format/codecs to mp4/h264/aac you need just write:
```php
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
```

To build command line which performs this transformation just call `$command->build()` which will return array of all command line arguments to achieve the goal:
```
[
    '-y',
    '-i', 'input.mov',
    '-movflags', 'faststart',
    '-map', '0:v:0', '-c:v:0', 'libx264', '-preset', 'veryfast', '-pix_fmt:v:0', 'yuv420p',
    '-map', '0:a:0', '-c:a:0', 'aac',
    'output.mp4',
]
```

More examples can be found in [CommandTest](http://github.com/javer/ffmpeg-transformer/blob/master/tests/Command/CommandTest.php).

Media profile builder
---------------------

Enables you to create `MediaProfile` for the given media file or from the given array.

From file:
```php
$ffmpeg = new FFMpeg\FFMpeg(...);
$inputVideo = $ffmpeg->open($filename);
$inputMediaProfile = MediaProfile::fromMedia($inputVideo);
```

From array:
```php
$referenceMediaProfile = MediaProfile::fromArray([
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
]);
```

Profile transformer
-------------------

Performs calculation of the transformation which should be applied to the input MediaProfile to get output MediaProfile (usually reference).

```php
$transformation = (new ProfileTransformer())
    ->transformMedia($sourceMediaProfile, $referenceMediaProfile);
```

It returns a new `MediaProfile` which contains only necessary parameters which should be changed.

Command transformer
-------------------

Builds a command for FFmpeg to perform necessary transformation (from the previous step) to transform input media file to the output media file.

```php
$command = (new CommandTransformer())
    ->applyTransformation($transformation, $inputFilename, $outputFilename);
```

It returns a `Command` (see the first section) which should be run by ffmpeg to convert input media file to the reference. 
```php
$ffmpeg->getFFMpegDriver()->command($command->build());
```
