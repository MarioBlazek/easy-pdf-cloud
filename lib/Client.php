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

use Exception;
use InvalidArgumentException;
use function count;
use function basename;
use function pathinfo;

class Client
{
    /**
     * @var RestApi
     */
    private $restApi;

    /**
     * Client constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param IOAuth2TokenManager|null $tokenManager
     * @param UrlInfo|null $urlInfo
     */
    public function __construct(string $clientId, string $clientSecret, IOAuth2TokenManager $tokenManager = null, UrlInfo $urlInfo = null)
    {
        $this->restApi = new RestApi($clientId, $clientSecret, $tokenManager, $urlInfo);
    }

    /**
     * Starts new job from file located in given path
     *
     * @param string $workflowId
     * @param string $filePath
     * @param bool $enableTestMode
     *
     * @return Job
     */
    public function startNewJobWithFilePath(string $workflowId, string $filePath, bool $enableTestMode = false): Job
    {
        $restApi = $this->restApi;

        $jobId = $restApi->createNewJobWithFilePath($workflowId, $filePath, true, $enableTestMode);

        return new Job($restApi, $jobId);
    }

    /**
     * Starts new job from file located in given path
     * also specifies name for result file
     *
     * @param string $workflowId
     * @param string $filePath
     * @param string $fileName
     * @param bool $enableTestMode
     *
     * @return Job
     */
    public function startNewJobWithFilePathAndName(string $workflowId, string $filePath, string $fileName, bool $enableTestMode = false): Job
    {
        $restApi = $this->restApi;

        $jobId = $restApi->createNewJobWithFilePathAndName($workflowId, $filePath, $fileName, true, $enableTestMode);

        return new Job($restApi, $jobId);
    }

    /**
     * Starts new job with given contents
     * and custom result file name
     *
     * @param string $workflowId
     * @param string $fileContents
     * @param string $fileName
     * @param bool $enableTestMode
     *
     * @return Job
     */
    public function startNewJobWithFileContents(string $workflowId, string $fileContents, string $fileName, bool $enableTestMode = false): Job
    {
        $restApi = $this->restApi;

        $jobId = $restApi->createNewJobWithFileContents($workflowId, $fileContents, $fileName, true, $enableTestMode);

        return new Job($restApi, $jobId);
    }

    /**
     * Starts new job that merges multiple file
     * into result file
     *
     * @param string $workflowId
     * @param array $filePaths
     * @param bool $enableTestMode
     * @return Job
     *
     * @throws Exception
     */
    public function startNewJobForMergeTask(string $workflowId, array $filePaths, bool $enableTestMode = false): Job
    {
        $restApi = $this->restApi;

        $filesCount = count($filePaths);

        if (0 === $filesCount) {
            throw new InvalidArgumentException('No input files specified');
        }

        $filePath = $filePaths[0];

        if (1 === $filesCount) {
            return $this->startNewJobWithFilePath($workflowId, $filePath);
        }

        $fileName = basename($filePath);

        $fileNameMap = array();
        $fileNameMap[$fileName] = true;

        $jobId = $restApi->createNewJobWithFilePathAndName($workflowId, $filePath, $fileName, false, $enableTestMode);

        try {
            for ($i = 1; $i < $filesCount; ++$i) {
                $filePath = $filePaths[$i];
                $fileName = basename($filePath);

                $pathInfo = pathinfo($filePath);
                $fName = (isset($pathInfo['filename']) ? $pathInfo['filename'] : '');
                $fExt = (isset($pathInfo['extension']) ? $pathInfo['extension'] : '');

                $fNameIndex = 0;
                while (isset($fileNameMap[$fileName])) {
                    ++$fNameIndex;
                    $fileName = $fName . ' (' . $fNameIndex . ').' . $fExt;
                }

                $fileNameMap[$fileName] = true;

                $restApi->uploadInputWithFilePathAndName($jobId, $filePath, $fileName);
            }

            $restApi->startJob($jobId);
        } catch (Exception $e) {
            try {
                $restApi->deleteJob($jobId);
            } catch (Exception $eInner) {
            }

            throw $e;
        }

        return new Job($restApi, $jobId);
    }
}
