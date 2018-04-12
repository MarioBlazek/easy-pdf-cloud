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

class JobInfo
{
    const STATUS_UNKNOWN = 0;
    const STATUS_WAITING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;
    const STATUS_CANCELLED = 4;

    private $jobId;
    private $workflowId;
    private $finished;
    private $status;
    private $progress;
    private $detail;

    public function __construct($jobId, $workflowId, $finished, $status, $progress, JobInfoDetail $detail = null)
    {
        $this->jobId = $jobId;
        $this->workflowId = $workflowId;
        $this->finished = $finished;
        $this->status = $status;
        $this->progress = $progress;
        $this->detail = $detail;
    }

    public function getJobId()
    {
        return $this->jobId;
    }

    public function getWorkflowId()
    {
        return $this->workflowId;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getFinished()
    {
        return $this->finished;
    }

    public function getProgress()
    {
        return $this->progress;
    }

    public function getDetail()
    {
        return $this->detail;
    }
}
