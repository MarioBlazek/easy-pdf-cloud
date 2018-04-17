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

class FileMetadata
{
    private $isFolder;
    private $name;
    private $bytes;
    private $mime;
    private $modifiedDate;
    private $contents;

    public function __construct($isFolder, $name, $bytes, $mime, $modifiedDate, array $contents = null)
    {
        $this->isFolder = $isFolder;
        $this->name = $name;
        $this->bytes = $bytes;
        $this->mime = $mime;
        $this->modifiedDate = $modifiedDate;
        $this->contents = $contents;
    }

    public function getIsFolder()
    {
        return $this->isFolder;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBytes()
    {
        return $this->bytes;
    }

    public function getMime()
    {
        return $this->mime;
    }

    public function getModifiedDate()
    {
        return $this->modifiedDate;
    }

    public function getContents()
    {
        return $this->contents;
    }
}
