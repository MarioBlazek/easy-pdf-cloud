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

class Job
{
    private $restApi;
    private $jobId;

    public function __construct(RestApi $restApi, $jobId)
    {
        $this->restApi = $restApi;
        $this->jobId = $jobId;
    }

    public function __destruct()
    {
        $restApi = $this->restApi;
        $jobId = $this->jobId;

        $this->restApi = null;
        $this->jobId = null;

        if (null !== $restApi && null !== $jobId) {
            try {
                $restApi->deleteJob($jobId);
            } catch (\Exception $e) {
            }
        }
    }

    public function getJobId()
    {
        return $this->jobId;
    }

    public function waitForJobExecutionCompletion()
    {
        $restApi = $this->restApi;
        $jobId = $this->jobId;

        $jobInfo = null;

        while (true) {
            $jobInfo = $restApi->waitForJobEvent($jobId);
            if ($jobInfo->getFinished()) {
                break;
            }
        }

        $status = $jobInfo->getStatus();
        if (JobInfo::STATUS_COMPLETED !== $status) {
            throw new JobExecutionException($jobInfo);
        }

        $fileData = $restApi->downloadOutput($jobId);

        return new JobExecutionResult($jobInfo, $fileData);
    }

    public function cancelJobExecution()
    {
        $restApi = $this->restApi;
        $jobId = $this->jobId;

        $restApi->stopJob($jobId);
    }
}
