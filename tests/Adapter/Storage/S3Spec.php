<?php

namespace tests\DocumentStorage\Adapter\Storage;

use Aws\S3\S3Client;
use PhpSpec\ObjectBehavior;
use DocumentStorage\Storage;

/**
 * @mixin \DocumentStorage\Adapter\Storage\S3
 */
class S3Spec extends ObjectBehavior
{
    public function let(S3Client $s3Client)
    {
        $this->beConstructedWith($s3Client, 'bucket');
    }

    public function it_implements_the_Storage_interface()
    {
        $this->shouldImplement(Storage::class);
    }
}
