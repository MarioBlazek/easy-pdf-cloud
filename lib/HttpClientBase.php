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

class HttpClientBase
{
    const CRLF = "\r\n";

    protected function stringStartsWith($source, $subString)
    {
        return 0 === \mb_strpos($source, $subString, 0, 'utf-8');
    }

    protected function stringEndsWith($source, $subString)
    {
        $sourceLength = \mb_strlen($source, 'utf-8');
        $subStringLength = \mb_strlen($subString, 'utf-8');
        $subStringIndex = $sourceLength - $subStringLength;

        if ($subStringLength < 0) {
            return false;
        }

        $newString = (\mb_substr($source, $subStringIndex, $subStringLength, 'utf-8'));

        return $newString === $subString;
    }

    protected function getValueFromArray(array $array, $index, $defaultValue = null)
    {
        return isset($array[$index]) ? $array[$index] : $defaultValue;
    }

    protected function getHttpResponseFromUrl($url, $context)
    {
        $http_response_header = null;

        $contents = @\file_get_contents($url, false, $context);
        if (false === $contents) {
            throw new EasyPdfCloudApiException(0, 'Unable to communicate to the server');
        }

        return array(
            'header' => $http_response_header,
            'contents' => $contents,
        );
    }

    protected function mapFirstLineHeaders($header)
    {
        $array = array();

        $keyValueMap = \array_map('trim', \explode(':', $header, 2));
        $headerName = (\count($keyValueMap) >= 1 ? $keyValueMap[0] : null);
        $headerValue = (\count($keyValueMap) >= 2 ? $keyValueMap[1] : null);

        $firstLineMap = \array_map('trim', \explode(' ', $headerName, 3));

        if (\count($firstLineMap) >= 1) {
            $httpVersionHeader = $firstLineMap[0];
            $httpVersionMap = \array_map('trim', \explode('/', $httpVersionHeader, 2));

            if (\count($httpVersionMap) >= 2) {
                $headerNameLC = 'http-version';
                $headerValue = $httpVersionMap[1];
                $array += array($headerNameLC => $headerValue);
            }

            if (\count($firstLineMap) >= 2) {
                $headerNameLC = 'status-code';
                $headerValue = $firstLineMap[1];
                $array += array($headerNameLC => $headerValue);

                if (\count($firstLineMap) >= 3) {
                    $headerNameLC = 'status-description';
                    $headerValue = $firstLineMap[2];
                    $array += array($headerNameLC => $headerValue);
                }
            }
        }

        return $array;
    }

    protected function mapHttpHeaders($headers)
    {
        $headersCount = \count($headers);
        if (0 === $headersCount) {
            return array();
        }

        $header = $headers[0];
        $array = $this->mapFirstLineHeaders($header);

        for ($i = 1; $i < $headersCount; ++$i) {
            $header = $headers[$i];

            $keyValueMap = \array_map('trim', \explode(':', $header, 2));
            $headerName = (\count($keyValueMap) >= 1 ? $keyValueMap[0] : null);
            $headerValue = (\count($keyValueMap) >= 2 ? $keyValueMap[1] : null);

            if (null !== $headerName) {
                $headerNameLC = \mb_strtolower($headerName, 'utf-8');
                $array += array($headerNameLC => $headerValue);
            }
        }

        return $array;
    }

    protected function getStatusCodeFromResponse($headers)
    {
        if (!isset($headers['status-code'])) {
            return 0;
        }

        $statusCode = (int) $headers['status-code'];

        return $statusCode;
    }

    protected function isSuccessfulResponse($headers)
    {
        $statusCode = $this->getStatusCodeFromResponse($headers);

        return $statusCode >= 200 && $statusCode < 300;
    }

    protected function getFileNameFromContentDisposisionHeader($contentDisposition)
    {
        if (!isset($contentDisposition)) {
            return null;
        }

        $colonSeparatedList = \array_map('trim', \explode(';', $contentDisposition));
        $array = array();

        foreach ($colonSeparatedList as $item) {
            $separatedList = \array_map('trim', \explode('=', $item, 2));

            if (count($separatedList) >= 2) {
                $nameLC = \mb_strtolower($separatedList[0], 'utf-8');
                $value = \trim($separatedList[1], '"');

                $array += array($nameLC => $value);
            }
        }

        if (isset($array['filename*'])) {
            $fileName = $array['filename*'];

            $utf8Prefix = 'utf-8\'\'';

            if ($this->stringStartsWith($fileName, $utf8Prefix)) {
                // trim utf8 prefix
                $prefixLength = \mb_strlen($utf8Prefix, 'utf-8');
                $fileName = \mb_substr($fileName, $prefixLength, null, 'utf-8');
            }

            // URL decode and return
            $fileName = \urldecode($fileName);

            return $fileName;
        } elseif (isset($array['filename'])) {
            $fileName = $array['filename'];

            $base64Prefix = '=?utf-8?B?';
            $base64Postfix = '?=';

            if ($this->stringStartsWith($fileName, $base64Prefix)) {
                // trim base64 prefix
                $prefixLength = \mb_strlen($base64Prefix, 'utf-8');
                $fileName = \mb_substr($fileName, $prefixLength, null, 'utf-8');

                if ($this->stringEndsWith($fileName, $base64Postfix)) {
                    // trim base64 postfix
                    $fileNameLength = \mb_strlen($fileName, 'utf-8');
                    $postfixLength = \mb_strlen($base64Postfix, 'utf-8');
                    $substrLength = $fileNameLength - $postfixLength;
                    $fileName = \mb_substr($fileName, 0, $substrLength, 'utf-8');
                }

                // base64 decode and return
                $fileName = \base64_decode($fileName, true);

                return $fileName;
            }

            // URL decode and return
            $fileName = \urldecode($fileName);

            return $fileName;
        }

        return null;
    }

    protected function parseWwwAuthenticateResonseHeader($wwwAuthenticate)
    {
        $spaceSeparatedList = \array_map('trim', \explode(' ', $wwwAuthenticate));
        $commaSeparatedList = array();
        $array = array();

        foreach ($spaceSeparatedList as $item) {
            $separatedList = \array_map('trim', \explode(',', $item));

            if (\count($separatedList) >= 2) {
                $commaSeparatedList = \array_merge($commaSeparatedList, $separatedList);
            }
        }

        foreach ($commaSeparatedList as $item) {
            $separatedList = \array_map('trim', \explode('=', $item, 2));

            if (\count($separatedList) >= 2) {
                $nameLC = \mb_strtolower(\trim($separatedList[0]), 'utf-8');
                $value = \urldecode(\trim(\trim($separatedList[1]), '"'));

                $array += array($nameLC => $value);
            }
        }

        return $array;
    }

    protected function checkWwwAuthenticateResponseHeader($headers)
    {
        $statusCode = $this->getStatusCodeFromResponse($headers);

        if (400 !== $statusCode && 401 !== $statusCode) {
            return;
        }

        if (!isset($headers['www-authenticate'])) {
            return;
        }

        $wwwAuthenticate = $headers['www-authenticate'];
        $wwwAuthenticateMap = $this->parseWwwAuthenticateResonseHeader($wwwAuthenticate);

        if (isset($wwwAuthenticateMap['error'])) {
            $error = $wwwAuthenticateMap['error'];

            $errorDescription = '';
            if (isset($wwwAuthenticateMap['error_description'])) {
                $errorDescription = $wwwAuthenticateMap['error_description'];
            }

            throw new ApiAuthorizationException($statusCode, $error, $errorDescription);
        }
    }

    protected function needsToRefreshToken($headers)
    {
        try {
            $this->checkWwwAuthenticateResponseHeader($headers);
        } catch (ApiAuthorizationException $e) {
            $error = $e->getError();
            if ('invalid_token' === $error || 'expired_token' === $error) {
                return true;
            }

            throw $e;
        }

        return false;
    }

    protected function decodeJsonFromResponse($headers, $contents, $failIfNotJson)
    {
        if (!isset($headers['content-type'])) {
            if ($failIfNotJson) {
                throw new EasyPdfCloudApiException(0, 'Unsupported response data format (only JSON is supported)');
            }

            return array(); // return empty array
        }

        $contentType = $headers['content-type'];

        if (!$this->stringStartsWith($contentType, 'application/json')) {
            if ($failIfNotJson) {
                throw new EasyPdfCloudApiException(0, 'Unsupported response data format (only JSON is supported)');
            }

            return array(); // return empty array
        }

        if (!isset($contents)) {
            return array(); // return empty array
        }

        $jsonResponse = \json_decode($contents, true);
        if (!isset($jsonResponse)) {
            return array(); // return empty array
        }

        return $jsonResponse;
    }

    protected function handleResponse($headers, $contents = null)
    {
        if ($this->isSuccessfulResponse($headers)) {
            // successful
            return true;
        } elseif ($this->needsToRefreshToken($headers)) {
            // need to refresh token & try again
            return false;
        }

        // failed

        $statusCode = $this->getStatusCodeFromResponse($headers);

        $statusDescription = 'Unknown error occurred';
        if (isset($headers['status-description'])) {
            $statusDescription = $headers['status-description'];
        }

        $jsonResponse = $this->decodeJsonFromResponse($headers, $contents, false);

        if (isset($jsonResponse['error'])) {
            $error = $jsonResponse['error'];
            throw new EasyPdfCloudApiException($statusCode, $statusDescription, $error);
        } elseif (isset($jsonResponse['message'])) {
            $message = $jsonResponse['message'];
            throw new EasyPdfCloudApiException($statusCode, $statusDescription, $message);
        }

        throw new EasyPdfCloudApiException($statusCode, $statusDescription);
    }
}
