<?php

namespace Bcl\EasyPdfCloud;

use PHPUnit\Framework\TestCase;

class JobInfoDetailTest extends TestCase
{
    public function testGettersDefault()
    {
        $info = new JobInfoDetail();

        $this->assertNull($info->getApiCredits());
        $this->assertNull($info->getOcrCredits());
        $this->assertTrue(is_array($info->getErrors()));
    }

    public function testGetters()
    {
        $credits1 = new CreditsInfo(120, false);
        $credits2= new CreditsInfo(12, false);

        $info = new JobInfoDetail($credits1, $credits2);

        $this->assertSame($credits1, $info->getApiCredits());
        $this->assertSame($credits2, $info->getOcrCredits());
        $this->assertTrue(is_array($info->getErrors()));
    }

    public function testGettersWithErrors()
    {
        $credits1 = new CreditsInfo(120, false);
        $credits2= new CreditsInfo(12, false);
        $errors = [
            'error' => 'error'
        ];

        $info = new JobInfoDetail($credits1, $credits2, $errors);

        $this->assertSame($credits1, $info->getApiCredits());
        $this->assertSame($credits2, $info->getOcrCredits());
        $this->assertEquals($errors, $info->getErrors());
    }
}