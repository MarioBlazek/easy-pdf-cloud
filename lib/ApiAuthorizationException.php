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
use function mb_strlen;

class ApiAuthorizationException extends Exception
{
    private $statusCode;
    private $error;
    private $errorDescription;

    public function __construct($statusCode, $error, $errorDescription, $code = 0, Exception $previous = null)
    {
        $message = static::toString($statusCode, $error, $errorDescription);

        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
        $this->error = $error;
        $this->errorDescription = $errorDescription;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    private static function toString($statusCode, $error, $errorDescription)
    {
        $string = (mb_strlen($errorDescription, Constraints::UTF_8) > 0 ? $errorDescription : $error);
        $string .= ' (HTTP status code: ' . $statusCode . ')';

        return $string;
    }
}
