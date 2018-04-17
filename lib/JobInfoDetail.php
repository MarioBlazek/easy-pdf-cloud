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

class JobInfoDetail
{
    /**
     * @var CreditsInfo|null
     */
    private $apiCredits;

    /**
     * @var CreditsInfo|null
     */
    private $ocrCredits;

    /**
     * @var array
     */
    private $errors;

    /**
     * JobInfoDetail constructor.
     *
     * @param CreditsInfo|null $apiCredits
     * @param CreditsInfo|null $ocrCredits
     * @param array $errors
     */
    public function __construct(?CreditsInfo $apiCredits = null, ?CreditsInfo $ocrCredits = null, array $errors = [])
    {
        $this->apiCredits = $apiCredits;
        $this->ocrCredits = $ocrCredits;
        $this->errors = $errors;
    }

    /**
     * @return CreditsInfo|null
     */
    public function getApiCredits(): ?CreditsInfo
    {
        return $this->apiCredits;
    }

    /**
     * @return CreditsInfo|null
     */
    public function getOcrCredits(): ?CreditsInfo
    {
        return $this->ocrCredits;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
