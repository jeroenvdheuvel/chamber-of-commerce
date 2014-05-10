<?php

namespace Werkspot\Component\ChamberOfCommerce\Exception;

class InvalidChamberOfCommerceStatusException extends ChamberOfCommerceRecordException
{
    /**
     * @param int|string $chamberOfCommerceNumber
     * @param string $status
     */
    public function __construct($chamberOfCommerceNumber, $status)
    {
        $message = sprintf(
            'Chamber of commerce record with number [%s] has invalid status [%s]',
            $chamberOfCommerceNumber, $status
        );

        parent::__construct($chamberOfCommerceNumber, $message);
    }
}