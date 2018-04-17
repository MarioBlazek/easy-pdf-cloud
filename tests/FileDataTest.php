<?php

namespace Bcl\EasyPdfCloud\Tests;

use Bcl\EasyPdfCloud\FileData;
use PHPUnit\Framework\TestCase;

class FileDataTest extends TestCase
{
    public function testGetters()
    {
        $data = new FileData('filename', 'contents', 1024,'content-type');

        $this->assertEquals('filename', $data->getName());
        $this->assertEquals('contents', $data->getContents());
        $this->assertEquals(1024, $data->getBytes());
        $this->assertEquals('content-type', $data->getContentType());
    }
}