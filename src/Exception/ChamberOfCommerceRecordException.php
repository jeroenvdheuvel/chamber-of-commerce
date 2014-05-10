<?php

namespace Werkspot\Component\ChamberOfCommerce\Exception;

use RuntimeException;
use Exception;

abstract class ChamberOfCommerceRecordException extends RuntimeException implements ChamberOfCommerceExceptionInterface
{
    /** @var string|int */
    private $chamberOfCommerceNumber;

    /**
     * @param string|int $chamberOfCommerceNumber
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($chamberOfCommerceNumber, $message = '', $code = 0, Exception $previous = null)
    {
        $this->chamberOfCommerceNumber = $chamberOfCommerceNumber;

        parent::__construct($message, $code, $previous);
    }
    /**
     * @return string|int
     */
    public function getChamberOfCommerceNumber()
    {
        return $this->chamberOfCommerceNumber;
    }
}