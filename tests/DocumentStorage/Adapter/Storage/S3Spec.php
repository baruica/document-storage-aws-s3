<?php declare(strict_types=1);

namespace DocumentStorage\Adapter\Storage;

use Aws\S3\S3Client;
use PhpSpec\ObjectBehavior;
use DocumentStorage\Storage;

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
