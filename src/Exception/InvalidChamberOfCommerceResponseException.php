<?php

namespace Werkspot\Component\ChamberOfCommerce\Exception;

use Exception;

class InvalidChamberOfCommerceResponseException extends ChamberOfCommerceRecordException implements ParseExceptionInterface
{
    /**
     * @param int|string $chamberOfCommerceNumber
     * @param string $content
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($chamberOfCommerceNumber, $content, $code = 0, Exception $previous = null)
    {
        $message = sprintf(
            'Got an invalid response for chamber of commerce number [%d], got response [%s]',
            $chamberOfCommerceNumber, $content
        );

        parent::__construct($message);
    }
}