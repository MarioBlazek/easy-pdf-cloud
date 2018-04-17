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

use Exception;
use function mb_strlen;
use function count;

class JobExecutionException extends Exception
{
    private $jobInfo;

    public function __construct(JobInfo $jobInfo = null, $code = 0, Exception $previous = null)
    {
        $message = static::toString($jobInfo);

        parent::__construct($message, $code, $previous);

        $this->jobInfo = $jobInfo;
    }

    public function getJobInfo()
    {
        return $this->jobInfo;
    }

    private static function toString(JobInfo $jobInfo)
    {
        $jobError = null;

        $jobDetail = $jobInfo->getDetail();
        if (null !== $jobDetail) {
            $jobErrors = $jobDetail->getErrors();
            if (null !== $jobErrors && count($jobErrors) > 0) {
                $jobError = $jobErrors[0];
            }
        }

        if (!$jobError instanceof JobError) {
            return 'Job execution failed with unknown error';
        }

        $message = $jobError->getMessage();
        $status = $jobInfo->getStatus();
        if (0 === mb_strlen($message, Constraints::UTF_8)) {
            if (JobInfo::STATUS_FAILED === $status) {
                return 'Job execution failed';
            } elseif (JobInfo::STATUS_CANCELLED === $status) {
                return 'Job execution cancelled';
            }

            return 'Job execution failed with unknown error';
        }

        $detail = $jobError->getDetail();
        if (0 === mb_strlen($detail, Constraints::UTF_8)) {
            return $message;
        }

        $string = $detail;

        $extraDetail = $jobError->getExtraDetail();
        if (mb_strlen($extraDetail, Constraints::UTF_8) > 0) {
            $string .= ' (' . $extraDetail . ')';
        }

        return $string;
    }
}
