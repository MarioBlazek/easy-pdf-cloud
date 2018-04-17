<?php

namespace Bcl\EasyPdfCloud\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Bcl\EasyPdfCloud\Exception\EasyPdfCloudApiException;

class EasyPdfCloudApiExceptionTest extends TestCase
{
    public function testGetters()
    {
        $exception = new EasyPdfCloudApiException(1, 'my error', 'description');

        $this->assertEquals(1, $exception->getStatusCode());
        $this->assertEquals('my error', $exception->getError());
        $this->assertEquals('description', $exception->getDescription());

        $this->assertEquals('description (HTTP status code: 1)', $exception->getMessage());
    }

    /**
     * @expectedException \Bcl\EasyPdfCloud\Exception\EasyPdfCloudApiException
     * @expectedExceptionMessage description (HTTP status code: 1)
     */
    public function testException()
    {
        throw new EasyPdfCloudApiException(1, 'my error', 'description');
    }
}