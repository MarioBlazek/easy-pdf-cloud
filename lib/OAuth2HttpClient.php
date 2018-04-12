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

class OAuth2HttpClient extends HttpClientBase
{
    private $clientId;
    private $clientSecret;
    private $tokenManager;
    private $urlInfo;

    public function __construct($clientId, $clientSecret, IOAuth2TokenManager $tokenManager, UrlInfo $urlInfo)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tokenManager = $tokenManager;
        $this->urlInfo = $urlInfo;
    }

    public function getNewAccessToken()
    {
        $url = $this->getOAuth2TokenEndPoint();

        $data = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => 'epc.api',
        );

        $postData = \http_build_query($data);

        $httpHeader = 'Content-Type: application/x-www-form-urlencoded' . static::CRLF;
        $httpHeader .= 'Accept: application/json; charset=utf-8' . static::CRLF;
        $httpHeader .= 'Content-Length: ' . \mb_strlen($postData, '8bit') . static::CRLF;

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

        if ($this->handleResponse($headers)) {
            $jsonResponse = $this->decodeJsonFromResponse($headers, $contents, true);

            if (!isset($jsonResponse['access_token'])) {
                $statusCode = $this->getStatusCodeFromResponse($headers);

                throw new EasyPdfCloudApiException(
                    $statusCode,
                    'Response from server does not contain access token'
                );
            }

            $accessToken = $jsonResponse['access_token'];

            if (!isset($jsonResponse['expires_in'])) {
                $statusCode = $this->getStatusCodeFromResponse($headers);

                throw new EasyPdfCloudApiException(
                    $statusCode,
                    'Response from server does not contain access token expiration'
                );
            }

            $expiresIn = $jsonResponse['expires_in'];

            if ($expiresIn > 120) {
                // we'll try to refresh a bit earlier
                $expiresIn -= 60;
            }

            $expirationTime = \time() + $expiresIn;

            $tokenInfo = array(
                'access_token' => $accessToken,
                'expiration_time' => $expirationTime,
            );

            $this->tokenManager->saveTokenInfo($tokenInfo);

            return $accessToken;
        }

        // This should raise an exception
        $this->checkWwwAuthenticateResponseHeader($headers);

        return null;
    }

    public function getAccessToken()
    {
        $tokenInfo = $this->tokenManager->loadTokenInfo();
        if (null === $tokenInfo) {
            return $this->getNewAccessToken();
        }

        if (!isset($tokenInfo['access_token'])) {
            return $this->getNewAccessToken();
        }

        $accessToken = $tokenInfo['access_token'];

        if (!isset($tokenInfo['expiration_time'])) {
            return $this->getNewAccessToken();
        }

        $expirationTime = $tokenInfo['expiration_time'];

        $timeNow = \time();
        if ($timeNow >= $expirationTime) {
            return $this->getNewAccessToken();
        }

        return $accessToken;
    }

    private function getOAuth2TokenEndPoint()
    {
        return $this->urlInfo->getOAuth2BaseUrl() . '/token';
    }
}
