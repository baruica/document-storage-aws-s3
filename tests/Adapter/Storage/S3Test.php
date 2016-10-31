<?php declare(strict_types=1);

namespace tests\DocumentStorage\Adapter\Storage;

use Aws\Common\Enum\Region;
use Aws\S3\S3Client;
use DocumentStorage\Adapter\Storage\S3;
use DocumentStorage\Exception\DocumentNotFound;
use DocumentStorage\Exception\DocumentNotStored;

class S3Test extends \PHPUnit_Framework_TestCase
{
    /** @var \Aws\S3\S3Client */
    protected static $s3_client;

    /** @var string */
    protected static $bucket;

    /** @var string */
    protected static $folder;

    /** @var \DocumentStorage\Adapter\Storage\S3 */
    protected static $tested;

    private $docNamesToStore = [
        'test-doc.txt',
    ];

    public static function setUpBeforeClass()
    {
        $processUser = posix_getpwuid(posix_geteuid());

        if (!file_exists(sprintf('/home/%s/.aws/credentials', $processUser['name']))) {
            throw new \PHPUnit_Framework_SkippedTestSuiteError('No credentials file found in home directory, skipping tests.');
        }

        $region = Region::EU_WEST_1;

        self::$s3_client = S3Client::factory(
            [
                'profile' => 'test_profile',
                'region' => $region,
            ]
        );

        self::$bucket = uniqid('document-storage-tests-', true);
        self::$folder = 'test folder';

        self::$s3_client->createBucket([
            'Bucket' => self::$bucket,
            'LocationConstraint' => $region,
        ]);

        self::$s3_client->waitUntilBucketExists([
            'Bucket' => self::$bucket,
        ]);

        self::$tested = new S3(
            self::$s3_client,
            self::$bucket,
            self::$folder
        );
    }

    public static function tearDownAfterClass()
    {
        self::$s3_client->clearBucket(self::$bucket);

        self::$s3_client->deleteBucket(['Bucket' => self::$bucket]);

        self::$s3_client->waitUntilBucketNotExists(['Bucket' => self::$bucket]);
    }

    public function provideStore() : array
    {
        $docsToStore = [];
        foreach ($this->docNamesToStore as $docName) {
            $docsToStore[] = [$docName, 'test body'];
        }

        return $docsToStore;
    }

    /**
     * @test
     * @dataProvider provideStore
     */
    public function store(string $docName, string $body)
    {
        $docUrl = self::$tested->store($body, $docName);

        static::assertStringEndsWith($docName, $docUrl);
    }

    /** @test */
    public function failing_storage_throws_an_exception()
    {
        $this->expectException(DocumentNotStored::class);

        self::$tested->store(
            (boolean) true, // invalid type for the pathOrBody
            'docName'
        );
    }

    public function provideDocNames() : array
    {
        return [
            $this->docNamesToStore,
        ];
    }

    /**
     * @test
     * @depends store
     * @dataProvider provideDocNames
     */
    public function get_url(string $docName)
    {
        $docUrl = self::$tested->getUrl($docName);

        static::assertStringEndsWith($docName, $docUrl);
    }

    /**
     * @test
     * @depends store
     */
    public function retrieve_if_doc_does_not_exist()
    {
        $this->expectException(DocumentNotFound::class);

        self::$tested->retrieve('non-existing-doc.txt');
    }
}
