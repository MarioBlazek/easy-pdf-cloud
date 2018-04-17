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

use Exception;
use function rtrim;
use function sys_get_temp_dir;
use function mkdir;
use function is_dir;
use function is_file;
use function file_get_contents;
use function file_put_contents;
use function unserialize;
use function serialize;
use function unlink;

class LocalFileTokenManager implements IOAuth2TokenManager
{
    private $tokenInfo;
    private $filePath;
    private $lockFilePath;

    public function __construct($clientId)
    {
        $tempDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);

        $tempFileDir = $tempDir;
        $tempFileDir .= DIRECTORY_SEPARATOR . 'easyPdfCloud';
        $tempFileDir .= DIRECTORY_SEPARATOR . 'clients';
        $tempFileDir .= DIRECTORY_SEPARATOR . $clientId;

        if (false === is_dir($tempFileDir)) {
            mkdir($tempFileDir, 0755, true);
        }

        $this->filePath = $tempFileDir . DIRECTORY_SEPARATOR . 'token.serialized';
        $this->lockFilePath = $tempFileDir . DIRECTORY_SEPARATOR . 'token.lock';
    }

    public function loadTokenInfo()
    {
        if (null === $this->tokenInfo && is_file($this->filePath)) {
            $lock = new FileLock($this->lockFilePath);

            try {
                if (null === $this->tokenInfo && is_file($this->filePath)) {
                    $serialized = @file_get_contents($this->filePath);
                    if (false !== $serialized) {
                        $this->tokenInfo = unserialize($serialized);
                    }
                }
            } catch (Exception $e) {
                unlink($this->filePath);
            }

            $lock->unlock();
            unset($lock);

            if (isset($e)) {
                throw $e;
            }
        }

        return $this->tokenInfo;
    }

    public function saveTokenInfo(array $tokenInfo)
    {
        $this->tokenInfo = $tokenInfo;

        $lock = new FileLock($this->lockFilePath);

        try {
            $serialized = serialize($tokenInfo);
            file_put_contents($this->filePath, $serialized);
        } catch (Exception $e) {
        }

        $lock->unlock();
        unset($lock);

        if (isset($e)) {
            throw $e;
        }
    }
}
