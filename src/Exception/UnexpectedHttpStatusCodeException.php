<?php

namespace Werkspot\Component\ChamberOfCommerce\Exception;

class UnexpectedHttpStatusCodeException extends ChamberOfCommerceRecordException
{
    /**
     * @param int|string $chamberOfCommerceNumber
     * @param int $expectedStatusCode
     * @param int $givenStatusCode
     * @param string $url
     */
    public function __construct($chamberOfCommerceNumber, $expectedStatusCode, $givenStatusCode, $url)
    {
        $message = sprintf(
            'Expected status code [%d] but got status code [%d] when calling url [%s] while retrieving chamber of commerce number [%s]',
            $expectedStatusCode, $givenStatusCode, $url, $chamberOfCommerceNumber
        );

        parent::__construct($chamberOfCommerceNumber, $message);
    }
}