<?php

namespace Bcl\EasyPdfCloud\Tests;

use PHPUnit\Framework\TestCase;
use Bcl\EasyPdfCloud\JobInfo;
use Bcl\EasyPdfCloud\FileData;
use Bcl\EasyPdfCloud\JobExecutionResult;

class JobExecutionResultTest extends TestCase
{
    public function testGetters()
    {
        $info = new JobInfo(123, 123, true, 1, 100);
        $data = new FileData('name', 'contents', 123, 'file');

        $result = new JobExecutionResult($info, $data);

        $this->assertSame($info, $result->getJobInfo());
        $this->assertSame($data, $result->getFileData());
    }
}