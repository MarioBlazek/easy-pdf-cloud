<?php

namespace Bcl\EasyPdfCloud\Tests;

use PHPUnit\Framework\TestCase;
use Bcl\EasyPdfCloud\CreditsInfo;

class CreditsInfoTest extends TestCase
{
    public function testGetters()
    {
        $info = new CreditsInfo(123, true);

        $this->assertEquals(123, $info->getCreditsRemaining());
        $this->assertTrue($info->getNotEnoughCredits());
    }
}