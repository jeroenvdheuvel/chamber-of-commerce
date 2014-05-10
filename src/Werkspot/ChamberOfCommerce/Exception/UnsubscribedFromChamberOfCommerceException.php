<?php

namespace Werkspot\Component\ChamberOfCommerce\Exception;

class UnsubscribedFromChamberOfCommerceException extends ChamberOfCommerceRecordException implements ChamberOfCommerceExceptionInterface
{
    /**
     * @param string $chamberOfCommerceNumber
     */
    public function __construct($chamberOfCommerceNumber)
    {
        $message = sprintf('Chamber of commerce number [%s] is unsubscribed', $chamberOfCommerceNumber);
        parent::__construct($chamberOfCommerceNumber, $message);
    }
}