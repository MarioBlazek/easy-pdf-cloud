<?php
/*
 * The MIT License
 *
 * Copyright 2016 BCL Technologies.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Bcl\EasyPdfCloud;

class WorkflowInfo
{
    private $workflowId;
    private $workflowName;
    private $monitorFolder;
    private $createdByUser;

    public function __construct($workflowId, $workflowName, $monitorFolder, $createdByUser)
    {
        $this->workflowId = $workflowId;
        $this->workflowName = $workflowName;
        $this->monitorFolder = $monitorFolder;
        $this->createdByUser = $createdByUser;
    }

    public function getWorkflowId()
    {
        return $this->workflowId;
    }

    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    public function getMonitorFolder()
    {
        return $this->monitorFolder;
    }

    public function getCreatedByUser()
    {
        return $this->createdByUser;
    }
}
