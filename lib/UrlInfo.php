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

class UrlInfo
{
    const AUTHORIZATION_SERVER_ENDPOINT = 'https://www.easypdfcloud.com/oauth2';
    const RESOURCE_SERVER_ENDPOINT = 'https://api.easypdfcloud.com/v1';

    private $oauth2BaseUrl;
    private $apiBaseUrl;

    public function __construct($oauth2BaseUrl = null, $apiBaseUrl = null)
    {
        $this->oauth2BaseUrl = $oauth2BaseUrl;
        $this->apiBaseUrl = $apiBaseUrl;

        if (0 === \mb_strlen($this->oauth2BaseUrl, 'utf-8')) {
            $this->oauth2BaseUrl = static::AUTHORIZATION_SERVER_ENDPOINT;
        }

        if (0 === \mb_strlen($this->apiBaseUrl, 'utf-8')) {
            $this->apiBaseUrl = static::RESOURCE_SERVER_ENDPOINT;
        }
    }

    public function getOAuth2BaseUrl()
    {
        return $this->oauth2BaseUrl;
    }

    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }
}
