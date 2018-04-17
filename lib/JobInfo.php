<?php

declare(strict_types=1);
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

    /**
     * @var string
     */
    private $jobId;

    /**
     * @var string
     */
    private $workflowId;

    /**
     * @var bool
     */
    private $finished;

    /**
     * @var int
     */
    private $status;

    /**
     * @var int
     */
    private $progress;

    /**
     * @var JobInfoDetail
     */
    private $detail;

    /**
     * JobInfo constructor.
     *
     * @param string $jobId
     * @param string $workflowId
     * @param bool $finished
     * @param int $status
     * @param int $progress
     * @param JobInfoDetail|null $detail
     */
    public function __construct(string $jobId, string $workflowId, bool $finished, int $status, int $progress, ?JobInfoDetail $detail = null)
    {
        $this->jobId = $jobId;
        $this->workflowId = $workflowId;
        $this->finished = $finished;
        $this->status = $status;
        $this->progress = $progress;
        $this->detail = $detail;
    }

    /**
     * @return string
     */
    public function getJobId(): string
    {
        return $this->jobId;
    }

    /**
     * @return string
     */
    public function getWorkflowId()
    {
        return $this->workflowId;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function getFinished(): bool
    {
        return $this->finished;
    }

    /**
     * @return int
     */
    public function getProgress(): int
    {
        return $this->progress;
    }

    /**
     * @return JobInfoDetail|null
     */
    public function getDetail(): ?JobInfoDetail
    {
        return $this->detail;
    }
}
