<?php

namespace Bcl\EasyPdfCloud;

use PHPUnit\Framework\TestCase;

class JobInfoTest extends TestCase
{
    public function testGettersDefault()
    {
        $info = new JobInfo(123, 123, true, 'finished', 21);

        $this->assertEquals(123, $info->getJobId());
        $this->assertEquals(123, $info->getWorkflowId());
        $this->assertEquals(true, $info->getFinished());
        $this->assertEquals('finished', $info->getStatus());
        $this->assertEquals(21, $info->getProgress());
        $this->assertNull($info->getDetail());
    }
    
    public function testGetters()
    {
        $jobDetail = new JobInfoDetail();
        $info = new JobInfo(123, 123, true, 'finished', 21, $jobDetail);

        $this->assertEquals(123, $info->getJobId());
        $this->assertEquals(123, $info->getWorkflowId());
        $this->assertEquals(true, $info->getFinished());
        $this->assertEquals('finished', $info->getStatus());
        $this->assertEquals(21, $info->getProgress());
        $this->assertSame($jobDetail, $info->getDetail());
    }
}