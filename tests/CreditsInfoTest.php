<?php

namespace Bcl\EasyPdfCloud;

use PHPUnit\Framework\TestCase;

class CreditsInfoTest extends TestCase
{
    public function testGetters()
    {
        $info = new CreditsInfo(123, true);

        $this->assertEquals(123, $info->getCreditsRemaining());
        $this->assertTrue($info->getNotEnoughCredits());
    }
}