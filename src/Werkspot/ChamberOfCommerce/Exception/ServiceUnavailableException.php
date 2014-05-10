<?php

namespace Werkspot\Component\ChamberOfCommerce\Exception;

class ServiceUnavailableException extends ChamberOfCommerceRecordException
{
    /**
     * @param int|string $chamberOfCommerceNumber
     * @param string $serviceName
     */
    public function __construct($chamberOfCommerceNumber, $serviceName)
    {
        $message = sprintf(
            'Service [%s] is unavailable while trying to retrieve chamber of commerce number [%s]',
            $serviceName, $chamberOfCommerceNumber
        );

        parent::__construct($chamberOfCommerceNumber, $message);
    }
}