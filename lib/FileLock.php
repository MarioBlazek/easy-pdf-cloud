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

use RuntimeException;
use function fopen;
use function flock;
use function fclose;

class FileLock
{
    private $file;

    public function __construct($lockFilePath)
    {
        $this->file = null;

        $file = fopen($lockFilePath, 'a');
        if (false === $file) {
            throw new RuntimeException('Unable to open the lock file');
        }

        if (false === flock($file, LOCK_EX)) {
            fclose($file);
            throw new RuntimeException('Unable to lock the file');
        }

        $this->file = $file;
    }

    public function __destruct()
    {
        $this->unlock();
    }

    public function unlock()
    {
        $file = $this->file;
        $this->file = null;

        if (null !== $file) {
            flock($file, LOCK_UN);
            fclose($file);
        }
    }
}
