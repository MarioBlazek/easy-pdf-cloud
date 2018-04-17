<?php

namespace Bcl\EasyPdfCloud\Tests\Exception;

use Bcl\EasyPdfCloud\Exception\ApiAuthorizationException;
use PHPUnit\Framework\TestCase;

class ApiAuthorizationExceptionTest extends TestCase
{
    public function testGetters()
    {
        $exception = new ApiAuthorizationException(2, 'my error', 'description');

        $this->assertEquals(2, $exception->getStatusCode());
        $this->assertEquals('my error', $exception->getError());
        $this->assertEquals('description', $exception->getDescription());

        $this->assertEquals('description (HTTP status code: 2)', $exception->getMessage());
    }

    /**
     * @expectedException \Bcl\EasyPdfCloud\Exception\ApiAuthorizationException
     * @expectedExceptionMessage description (HTTP status code: 2)
     */
    public function testException()
    {
        throw new ApiAuthorizationException(2, 'my error', 'description');
    }
}