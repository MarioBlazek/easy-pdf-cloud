<?php

namespace Bcl\EasyPdfCloud;

use PHPUnit\Framework\TestCase;

class FileMetadataTest extends TestCase
{
    public function testGettersDefault()
    {
        $metadata = new FileMetadata(true, 'name', 123, 'text', 'date');

        $this->assertTrue($metadata->getIsFolder());
        $this->assertEquals('name', $metadata->getName());
        $this->assertEquals(123, $metadata->getBytes());
        $this->assertEquals('text', $metadata->getMime());
        $this->assertEquals('date', $metadata->getModifiedDate());
        $this->assertNull($metadata->getContents());
    }

    public function testGetters()
    {
        $metadata = new FileMetadata(true, 'name', 123, 'text', 'date', ['contents' => 'data']);

        $this->assertTrue($metadata->getIsFolder());
        $this->assertEquals('name', $metadata->getName());
        $this->assertEquals(123, $metadata->getBytes());
        $this->assertEquals('text', $metadata->getMime());
        $this->assertEquals('date', $metadata->getModifiedDate());
        $this->assertEquals(['contents' => 'data'], $metadata->getContents());
    }
}