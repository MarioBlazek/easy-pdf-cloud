<?php

namespace Bcl\EasyPdfCloud\Exception;

use Exception;
use Bcl\EasyPdfCloud\StringUtils;

abstract class BaseException extends Exception
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var string
     */
    protected $description;

    /**
     * BaseException constructor.
     *
     * @param int $statusCode
     * @param string $error
     * @param string $description
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(int $statusCode, string $error, string $description, $code = 0, Exception $previous = null)
    {
        $message = $this->toString($statusCode, $error, $description);

        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode;
        $this->error = $error;
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $statusCode
     * @param string $error
     * @param string $description
     *
     * @return string
     */
    protected function toString(int $statusCode, string $error, string $description): string
    {
        $string = (StringUtils::length($description) > 0 ? $description : $error);
        $string .= ' (HTTP status code: ' . $statusCode . ')';

        return $string;
    }
}