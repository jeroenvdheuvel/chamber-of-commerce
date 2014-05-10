<?php

namespace Werkspot\Component\ChamberOfCommerce\Exception;

class NotFoundException extends ChamberOfCommerceRecordException
{
    /**
     * @param string|int $chamberOfCommerceNumber
     */
    public function __construct($chamberOfCommerceNumber)
    {
        $message = sprintf('Chamber of commerce number [%s] could not be found', $chamberOfCommerceNumber);

        parent::__construct($chamberOfCommerceNumber, $message);
    }
}