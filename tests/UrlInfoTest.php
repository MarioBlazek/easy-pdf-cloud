<?php

namespace Bcl\EasyPdfCloud\Tests;

use PHPUnit\Framework\TestCase;
use Bcl\EasyPdfCloud\UrlInfo;

class UrlInfoTest extends TestCase
{
    public function testGettersWithDefaultSettings()
    {
        $info = new UrlInfo();

        $this->assertEquals(UrlInfo::AUTHORIZATION_SERVER_ENDPOINT, $info->getOAuth2BaseUrl());
        $this->assertEquals(UrlInfo::RESOURCE_SERVER_ENDPOINT, $info->getApiBaseUrl());
    }

    public function testGetters()
    {
        $info = new UrlInfo('my url', 'api base url');

        $this->assertEquals('my url', $info->getOAuth2BaseUrl());
        $this->assertEquals('api base url', $info->getApiBaseUrl());
    }
}