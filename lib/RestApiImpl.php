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

class RestApiImpl extends OAuth2HttpClient
{
    private $urlInfo;

    public function __construct($clientId, $clientSecret, IOAuth2TokenManager $tokenManager = null, UrlInfo $urlInfo = null)
    {
        if (0 === \mb_strlen($clientId, 'utf-8')) {
            throw new \InvalidArgumentException('client ID is not specified');
        }

        if (0 === \mb_strlen($clientSecret, 'utf-8')) {
            throw new \InvalidArgumentException('client secret is not specified');
        }

        if (null === $tokenManager) {
            $tokenManager = new LocalFileTokenManager($clientId);
        }

        if (null === $urlInfo) {
            $urlInfo = new UrlInfo();
        }

        $this->urlInfo = $urlInfo;

        parent::__construct($clientId, $clientSecret, $tokenManager, $urlInfo);
    }

    public function validateWorkflowId($workflowId)
    {
        if (0 === \mb_strlen($workflowId, 'utf-8')) {
            throw new \InvalidArgumentException('Workflow ID is not specified');
        }

        if (16 !== \mb_strlen($workflowId, 'utf-8')) {
            throw new \InvalidArgumentException('Workflow ID is invalid');
        }
    }

    public function validateJobId($jobId)
    {
        if (0 === \mb_strlen($jobId, 'utf-8')) {
            throw new \InvalidArgumentException('Job ID is not specified');
        }

        if (16 !== \mb_strlen($jobId, 'utf-8')) {
            throw new \InvalidArgumentException('Job ID is invalid');
        }
    }

    public function getWorkflowInfoList($autoRenewAccessToken)
    {
        $accessToken = $this->getAccessToken();

        $url = $this->getWorkflowsEndPoint();

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;
        $httpHeader .= 'Accept: application/json; charset=utf-8' . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'GET',
                'header' => $httpHeader,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $jsonResponse = $this->decodeJsonFromResponse($headers, $contents, true);

            $workflowList = array();

            if (!isset($jsonResponse['workflows'])) {
                return $workflowList; // return empty array
            }

            $workflows = $jsonResponse['workflows'];

            foreach ($workflows as $workflow) {
                $workflowId = $this->getValueFromArray($workflow, 'workflowID', '');
                $workflowName = $this->getValueFromArray($workflow, 'workflowName', '');
                $monitorFolder = $this->getValueFromArray($workflow, 'monitorFolder', false);
                $createdByUser = $this->getValueFromArray($workflow, 'createdByUser', false);

                $workflowInfo = new WorkflowInfo($workflowId, $workflowName, $monitorFolder, $createdByUser);

                \array_push($workflowList, $workflowInfo);
            }

            return $workflowList;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();

            return $this->getWorkflowInfoList(false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);

        return null;
    }

    public function getWorkflowInfo($workflowId, $autoRenewAccessToken)
    {
        $accessToken = $this->getAccessToken();

        $url = $this->getWorkflowsEndPoint() . '/' . $workflowId;

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;
        $httpHeader .= 'Accept: application/json; charset=utf-8' . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'GET',
                'header' => $httpHeader,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $jsonResponse = $this->decodeJsonFromResponse($headers, $contents, true);

            $monitorFolder = $this->getValueFromArray($jsonResponse, 'monitorFolder', false);
            $workflowName = $this->getValueFromArray($jsonResponse, 'workflowName', '');

            $workflowInfo = new WorkflowInfo($workflowId, $monitorFolder, $workflowName);

            return $workflowInfo;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();

            return $this->getWorkflowInfo($workflowId, false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);

        return null;
    }

    public function createNewJobWithFileContents($workflowId, $fileContents, $fileName, $start, $test, $autoRenewAccessToken)
    {
        $fileType = 'application/octet-stream';

        //$finfo = finfo_open(FILEINFO_MIME_TYPE);
        //$fileType = finfo_file($finfo, $filePath);
        //finfo_close($finfo);

        $accessToken = $this->getAccessToken();

        $url = $this->getWorkflowsEndPoint() . '/' . $workflowId . '/jobs';
        $url .= '?file=' . \urlencode($fileName);
        $url .= '&start=' . ($start ? 'true' : 'false');
        $url .= '&test=' . ($test ? 'true' : 'false');

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;
        $httpHeader .= 'Accept: application/json; charset=utf-8' . static::CRLF;
        $httpHeader .= 'Content-Length: ' . \mb_strlen($fileContents, '8bit') . static::CRLF;
        $httpHeader .= 'Content-Type: ' . $fileType . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'PUT',
                'header' => $httpHeader,
                'content' => $fileContents,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $jsonResponse = $this->decodeJsonFromResponse($headers, $contents, true);

            $jobId = $this->getValueFromArray($jsonResponse, 'jobID', '');

            return $jobId;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();

            return $this->createNewJobWithFileContents($workflowId, $fileContents, $fileName, $start, $test, false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);

        return null;
    }

    public function uploadInputWithFileContents($jobId, $fileContents, $fileName, $autoRenewAccessToken)
    {
        $fileType = 'application/octet-stream';

        //$finfo = finfo_open(FILEINFO_MIME_TYPE);
        //$fileType = finfo_file($finfo, $filePath);
        //finfo_close($finfo);

        $accessToken = $this->getAccessToken();

        $url = $this->getJobsEndPoint() . '/' . $jobId . '/input/' . \urlencode($fileName);

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;
        $httpHeader .= 'Accept: application/json; charset=utf-8' . static::CRLF;
        $httpHeader .= 'Content-Length: ' . \mb_strlen($fileContents, '8bit') . static::CRLF;
        $httpHeader .= 'Content-Type: ' . $fileType . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'PUT',
                'header' => $httpHeader,
                'content' => $fileContents,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $this->decodeJsonFromResponse($headers, $contents, false);

            return;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();
            $this->uploadInputWithFileContents($jobId, $fileContents, $fileName, false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);
    }

    public function getOutputInfoForFileName($jobId, $fileName, $autoRenewAccessToken)
    {
        $accessToken = $this->getAccessToken();

        $fileNameSpecified = \mb_strlen($fileName, 'utf-8') > 0;

        $url = $this->getJobsEndPoint() . '/' . $jobId . '/output';
        $url .= ($fileNameSpecified ? '/' . \urlencode($fileName) : '');
        $url .= '?type=metadata';

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'GET',
                'header' => $httpHeader,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $jsonResponse = $this->decodeJsonFromResponse($headers, $contents, true);

            $fileMetadata = $this->getFileInfoFromJsonResponse($jsonResponse);

            return $fileMetadata;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();

            return $this->getOutputInfoForFileName($jobId, $fileName, false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);

        return null;
    }

    public function downloadOutputForFileName($jobId, $fileName, $autoRenewAccessToken)
    {
        $accessToken = $this->getAccessToken();

        $fileNameSpecified = \mb_strlen($fileName, 'utf-8') > 0;

        $url = $this->getJobsEndPoint() . '/' . $jobId . '/output';
        $url .= ($fileNameSpecified ? '/' . \urlencode($fileName) : '');
        $url .= '?type=file';

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'GET',
                'header' => $httpHeader,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $outputFileName = $fileName;

            if (0 === \mb_strlen($outputFileName, 'utf-8')) {
                if (isset($headers['content-disposition'])) {
                    $contentDisposition = $headers['content-disposition'];
                    $outputFileName = $this->getFileNameFromContentDisposisionHeader($contentDisposition);

                    if (0 === \mb_strlen($outputFileName, 'utf-8')) {
                        $outputFileName = 'output';
                    }
                }
            }

            $fileBytes = \mb_strlen($contents, '8bit');

            $contentType = 'application/octet-stream';
            if (isset($headers['content-type'])) {
                $contentType = $headers['content-type'];
            }

            $fileData = new FileData($outputFileName, $contents, $fileBytes, $contentType);

            return $fileData;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();

            return $this->downloadOutputForFileName($jobId, $fileName, false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);

        return null;
    }

    public function getJobInfo($jobId, $autoRenewAccessToken)
    {
        $accessToken = $this->getAccessToken();

        $url = $this->getJobsEndPoint() . '/' . $jobId;

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;
        $httpHeader .= 'Accept: application/json; charset=utf-8' . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'GET',
                'header' => $httpHeader,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $jsonResponse = $this->decodeJsonFromResponse($headers, $contents, true);

            $jobInfo = $this->getJobInfoFromJsonResponse($jsonResponse);

            return $jobInfo;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();

            return $this->getJobInfo($jobId, false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);

        return null;
    }

    public function startOrStopJob($jobId, $start, $autoRenewAccessToken)
    {
        $accessToken = $this->getAccessToken();

        $postData = \http_build_query(
            array(
                'operation' => ($start ? 'start' : 'stop'),
            )
        );

        $url = $this->getJobsEndPoint() . '/' . $jobId;

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;
        $httpHeader .= 'Accept: application/json; charset=utf-8' . static::CRLF;
        $httpHeader .= 'Content-Length: ' . \mb_strlen($postData, '8bit') . static::CRLF;
        $httpHeader .= 'Content-Type: application/x-www-form-urlencoded' . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'POST',
                'header' => $httpHeader,
                'content' => $postData,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $this->decodeJsonFromResponse($headers, $contents, false);

            return;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();
            $this->startOrStopJob($jobId, $start, false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);
    }

    public function deleteJob($jobId, $autoRenewAccessToken)
    {
        $accessToken = $this->getAccessToken();

        $url = $this->getJobsEndPoint() . '/' . $jobId;

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;
        $httpHeader .= 'Accept: application/json; charset=utf-8' . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'DELETE',
                'header' => $httpHeader,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $this->decodeJsonFromResponse($headers, $contents, false);

            return;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();
            $this->deleteJob($jobId, false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);
    }

    public function waitForJobEvent($jobId, $autoRenewAccessToken)
    {
        $accessToken = $this->getAccessToken();

        $url = $this->getJobsEndPoint() . '/' . $jobId . '/event';

        $httpHeader = 'Authorization: Bearer ' . $accessToken . static::CRLF;
        $httpHeader .= 'Accept: application/json; charset=utf-8' . static::CRLF;
        $httpHeader .= 'Content-Length: 0' . static::CRLF;
        $httpHeader .= 'Content-Type: application/x-www-form-urlencoded' . static::CRLF;

        $options = array(
            'http' => array(
                'ignore_errors' => true,
                'method' => 'POST',
                'header' => $httpHeader,
            ),
        );

        $context = \stream_context_create($options);

        $httpResponse = $this->getHttpResponseFromUrl($url, $context);
        $http_response_header = $httpResponse['header'];
        $contents = $httpResponse['contents'];

        $headers = $this->mapHttpHeaders($http_response_header);

        if ($this->handleResponse($headers, $contents)) {
            $statusCode = $this->getStatusCodeFromResponse($headers);
            if (202 === $statusCode) {
                // Job execution is not completed yet
                return new JobInfo($jobId, '', false, JobInfo::STATUS_WAITING, 0, null);
            }

            $jsonResponse = $this->decodeJsonFromResponse($headers, $contents, true);

            $jobInfo = $this->getJobInfoFromJsonResponse($jsonResponse);

            return $jobInfo;
        }

        if ($autoRenewAccessToken) {
            $this->getNewAccessToken();

            return $this->waitForJobEvent($jobId, false);
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);

        return null;
    }

    public static function getJobInfoStatusFromString($statusString)
    {
        if (\mb_strlen($statusString, 'utf-8') > 0) {
            if ('waiting' === $statusString) {
                return JobInfo::STATUS_WAITING;
            } elseif ('completed' === $statusString) {
                return JobInfo::STATUS_COMPLETED;
            } elseif ('failed' === $statusString) {
                return JobInfo::STATUS_FAILED;
            } elseif ('cancelled' === $statusString) {
                return JobInfo::STATUS_CANCELLED;
            }
        }

        return JobInfo::STATUS_UNKNOWN;
    }

    private function getWorkflowsEndPoint()
    {
        return $this->urlInfo->getApiBaseUrl() . '/workflows';
    }

    private function getJobsEndPoint()
    {
        return $this->urlInfo->getApiBaseUrl() . '/jobs';
    }

    private function getFileInfoFromJsonResponse($jsonResponse)
    {
        $isFolder = $this->getValueFromArray($jsonResponse, 'isFolder', false);
        $name = $this->getValueFromArray($jsonResponse, 'name', '');
        $bytes = $this->getValueFromArray($jsonResponse, 'bytes', 0);
        $mime = $this->getValueFromArray($jsonResponse, 'mime', 'application/octet-stream');
        $modifiedDate = $this->getValueFromArray($jsonResponse, 'modifiedDate', '');
        $contents = null;

        if ($isFolder) {
            $contents = array();

            if (isset($jsonResponse['contents'])) {
                $contentsJson = $jsonResponse['contents'];

                foreach ($contentsJson as $contentJson) {
                    $isFolderInner = $this->getValueFromArray($contentJson, 'isFolder', false);
                    $nameInner = $this->getValueFromArray($contentJson, 'name', '');
                    $bytesInner = $this->getValueFromArray($contentJson, 'bytes', 0);
                    $mimeInner = $this->getValueFromArray($contentJson, 'mime', 'application/octet-stream');
                    $modifiedDateInner = $this->getValueFromArray($contentJson, 'modifiedDate', '');

                    $fileMetadataInner = new FileMetadata(
                        $isFolderInner,
                        $nameInner,
                        $bytesInner,
                        $mimeInner,
                        $modifiedDateInner,
                        null
                    );

                    \array_push($contents, $fileMetadataInner);
                }
            }
        }

        $fileMetadata = new FileMetadata($isFolder, $name, $bytes, $mime, $modifiedDate, $contents);

        return $fileMetadata;
    }

    private function getJobInfoFromJsonResponse($jsonResponse)
    {
        $jobId = $this->getValueFromArray($jsonResponse, 'jobID', '');
        $workflowId = $this->getValueFromArray($jsonResponse, 'workflowID', '');
        $finished = $this->getValueFromArray($jsonResponse, 'finished', false);
        $progress = $this->getValueFromArray($jsonResponse, 'progress', 0);

        $statusString = $this->getValueFromArray($jsonResponse, 'status', 'unknown');
        $status = $this->getJobInfoStatusFromString($statusString);

        $apiCredits = null;
        $ocrCredits = null;
        $detail = null;

        if (isset($jsonResponse['detail'])) {
            $detailJson = $jsonResponse['detail'];
            $errors = null;

            if (isset($detailJson['apiCredits'])) {
                $apiCreditsJson = $detailJson['apiCredits'];

                $creditsRemaining = $this->getValueFromArray($apiCreditsJson, 'creditsRemaining', 0);
                $notEnoughCredits = $this->getValueFromArray($apiCreditsJson, 'notEnoughCredits', false);

                $apiCredits = new CreditsInfo($creditsRemaining, $notEnoughCredits);
            }

            if (isset($detailJson['ocrCredits'])) {
                $ocrCreditsJson = $detailJson['ocrCredits'];

                $creditsRemaining = $this->getValueFromArray($ocrCreditsJson, 'creditsRemaining', 0);
                $notEnoughCredits = $this->getValueFromArray($ocrCreditsJson, 'notEnoughCredits', false);

                $ocrCredits = new CreditsInfo($creditsRemaining, $notEnoughCredits);
            }

            if (isset($detailJson['errors'])) {
                $errorsJson = $detailJson['errors'];
                $errors = array();

                foreach ($errorsJson as $errorJson) {
                    $taskName = $this->getValueFromArray($errorJson, 'taskName', '');
                    $fileName = $this->getValueFromArray($errorJson, 'fileName', '');
                    $message = $this->getValueFromArray($errorJson, 'message', '');
                    $detail = $this->getValueFromArray($errorJson, 'detail', null);
                    $extraDetail = $this->getValueFromArray($errorJson, 'extraDetail', null);

                    $error = new JobError($taskName, $fileName, $message, $detail, $extraDetail);

                    \array_push($errors, $error);
                }
            }

            $detail = new JobInfoDetail($apiCredits, $ocrCredits, $errors);
        }

        return new JobInfo($jobId, $workflowId, $finished, $status, $progress, $detail);
    }
}
