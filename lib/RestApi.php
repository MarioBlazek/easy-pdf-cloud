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

class RestApi
{
    private $impl;

    public function __construct($clientId, $clientSecret, IOAuth2TokenManager $tokenManager = null, UrlInfo $urlInfo = null)
    {
        $this->impl = new RestApiImpl($clientId, $clientSecret, $tokenManager, $urlInfo);
    }

    // GET: /workflows
    public function getWorkflowInfoList()
    {
        return $this->impl->getWorkflowInfoList(true);
    }

    // GET: /workflows/{id}
    public function getWorkflowInfo($workflowId)
    {
        $this->impl->validateWorkflowId($workflowId);

        return $this->impl->getWorkflowInfo($workflowId, true);
    }

    // PUT: /workflows/{id}/job
    public function createNewJobWithFilePath($workflowId, $filePath, $start, $test)
    {
        $this->impl->validateWorkflowId($workflowId);

        if (0 === \mb_strlen($filePath, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('Input file is not specified');
        }

        if (false === \is_file($filePath)) {
            throw new \InvalidArgumentException('Input file does not exist');
        }

        $fileName = \basename($filePath);

        $fileContents = @\file_get_contents($filePath);
        if (false === $fileContents) {
            throw new \InvalidArgumentException('Unable to open input file');
        }

        return $this->impl->createNewJobWithFileContents($workflowId, $fileContents, $fileName, $start, $test, true);
    }

    // PUT: /workflows/{id}/job
    public function createNewJobWithFilePathAndName($workflowId, $filePath, $fileName, $start, $test)
    {
        $this->impl->validateWorkflowId($workflowId);

        if (0 === \mb_strlen($filePath, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('Input file is not specified');
        }

        if (0 === \mb_strlen($fileName, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('File name is not specified');
        }

        if (false === \is_file($filePath)) {
            throw new \InvalidArgumentException('Input file does not exist');
        }

        $fileContents = @\file_get_contents($filePath);
        if (false === $fileContents) {
            throw new \InvalidArgumentException('Unable to open input file');
        }

        return $this->impl->createNewJobWithFileContents($workflowId, $fileContents, $fileName, $start, $test, true);
    }

    // PUT: /workflows/{id}/job
    public function createNewJobWithFileContents($workflowId, $fileContents, $fileName, $start, $test)
    {
        $this->impl->validateWorkflowId($workflowId);

        if (null === $fileContents || false === $fileContents) {
            throw new \InvalidArgumentException('Input file is not specified');
        }

        if (0 === \mb_strlen($fileName, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('File name is not specified');
        }

        return $this->impl->createNewJobWithFileContents($workflowId, $fileContents, $fileName, $start, $test, true);
    }

    // PUT: /jobs/{id}
    public function uploadInputWithFilePath($jobId, $filePath)
    {
        $this->impl->validateJobId($jobId);

        if (0 === \mb_strlen($filePath, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('Input file is not specified');
        }

        if (false === \is_file($filePath)) {
            throw new \InvalidArgumentException('Input file does not exist');
        }

        $fileName = \basename($filePath);

        $fileContents = \file_get_contents($filePath);
        if (false === $fileContents) {
            throw new \InvalidArgumentException('Unable to open input file');
        }

        return $this->impl->uploadInputWithFileContents($jobId, $fileContents, $fileName, true);
    }

    // PUT: /jobs/{id}
    public function uploadInputWithFilePathAndName($jobId, $filePath, $fileName)
    {
        $this->impl->validateJobId($jobId);

        if (0 === \mb_strlen($filePath, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('Input file is not specified');
        }

        if (0 === \mb_strlen($fileName, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('File name is not specified');
        }

        if (false === \is_file($filePath)) {
            throw new \InvalidArgumentException('Input file does not exist');
        }

        $fileContents = \file_get_contents($filePath);
        if (false === $fileContents) {
            throw new \InvalidArgumentException('Unable to open input file');
        }

        return $this->impl->uploadInputWithFileContents($jobId, $fileContents, $fileName, true);
    }

    // PUT: /jobs/{id}/<filename>
    public function uploadInputWithFileContents($jobId, $fileContents, $fileName)
    {
        $this->impl->validateJobId($jobId);

        if (null === $fileContents || false === $fileContents) {
            throw new \InvalidArgumentException('Input file is not specified');
        }

        if (0 === \mb_strlen($fileName, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('File name is not specified');
        }

        return $this->impl->uploadInputWithFileContents($jobId, $fileContents, $fileName, true);
    }

    // GET: /jobs/{id}/output?type=metadata
    public function getOutputInfo($jobId)
    {
        $this->impl->validateJobId($jobId);

        return $this->impl->getOutputInfoForFileName($jobId, null, true);
    }

    // GET: /jobs/{id}/output/<filename>?type=metadata
    public function getOutputInfoForFileName($jobId, $fileName)
    {
        $this->impl->validateJobId($jobId);

        if (0 === \mb_strlen($fileName, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('File name is not specified');
        }

        return $this->impl->getOutputInfoForFileName($jobId, $fileName, true);
    }

    // GET: /jobs/{id}/output?type=file
    public function downloadOutput($jobId)
    {
        $this->impl->validateJobId($jobId);

        return $this->impl->downloadOutputForFileName($jobId, null, true);
    }

    // GET: /jobs/{id}/output/<filename>?type=file
    public function downloadOutputForFileName($jobId, $fileName)
    {
        $this->impl->validateJobId($jobId);

        if (0 === \mb_strlen($fileName, Constraints::UTF_8)) {
            throw new \InvalidArgumentException('File name is not specified');
        }

        return $this->impl->downloadOutputForFileName($jobId, $fileName, true);
    }

    // GET: /jobs/{id}
    public function getJobInfo($jobId)
    {
        $this->impl->validateJobId($jobId);

        return $this->impl->getJobInfo($jobId, true);
    }

    // POST: /jobs/{id}?operation=start
    public function startJob($jobId)
    {
        $this->impl->validateJobId($jobId);

        $this->impl->startOrStopJob($jobId, true, true);
    }

    // POST: /jobs/{id}?operation=stop
    public function stopJob($jobId)
    {
        $this->impl->validateJobId($jobId);

        $this->impl->startOrStopJob($jobId, false, true);
    }

    // POST: /jobs/{id}?operation=stop
    public function deleteJob($jobId)
    {
        $this->impl->validateJobId($jobId);

        $this->impl->deleteJob($jobId, true);
    }

    // POST: /jobs/{id}/event
    public function waitForJobEvent($jobId)
    {
        $this->impl->validateJobId($jobId);

        return $this->impl->waitForJobEvent($jobId, true);
    }
}
