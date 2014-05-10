<?php

namespace Werkspot\Component\ChamberOfCommerce\Exception;

use RuntimeException;

class ServiceUnavailableException extends RuntimeException implements ChamberOfCommerceExceptionInterface
{
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct(sprintf('Service [%s] is unavailable', $name));
    }
}