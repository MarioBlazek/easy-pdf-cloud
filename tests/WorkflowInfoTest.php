<?php

namespace Bcl\EasyPdfCloud;

use PHPUnit\Framework\TestCase;

class WorkflowInfoTest extends TestCase
{
    public function testGetters()
    {
        $info = new WorkflowInfo('123', 'name', 'folder', 'user');

        $this->assertEquals('123', $info->getWorkflowId());
        $this->assertEquals('name', $info->getWorkflowName());
        $this->assertEquals('folder', $info->getMonitorFolder());
        $this->assertEquals('user', $info->getCreatedByUser());
    }
}