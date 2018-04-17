<?php

namespace Bcl\EasyPdfCloud;

use PHPUnit\Framework\TestCase;

class JobErrorTest extends TestCase
{
    public function testGetters()
    {
        $error = new JobError('taskName', 'fileName', 'message', 'detail', 'extraDetail');

        $this->assertEquals('taskName', $error->getTaskName());
        $this->assertEquals('fileName', $error->getFileName());
        $this->assertEquals('message', $error->getMessage());
        $this->assertEquals('detail', $error->getDetail());
        $this->assertEquals('extraDetail', $error->getExtraDetail());
    }
}