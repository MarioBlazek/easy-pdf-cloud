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

class FileData
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $contents;

    /**
     * @var int
     */
    private $bytes;

    /**
     * @var string
     */
    private $contentType;

    /**
     * FileData constructor.
     *
     * @param string $fileName
     * @param string $contents
     * @param int $bytes
     * @param string $contentType
     */
    public function __construct(string $fileName, string $contents, int $bytes, string $contentType)
    {
        $this->name = $fileName;
        $this->contents = $contents;
        $this->bytes = $bytes;
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * @return int
     */
    public function getBytes(): int
    {
        return $this->bytes;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }
}
