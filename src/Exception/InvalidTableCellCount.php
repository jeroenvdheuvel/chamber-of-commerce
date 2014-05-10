<?php

namespace Werkspot\Component\ChamberOfCommerce\Exception;

use RuntimeException;

class InvalidTableCellCount extends RuntimeException implements ParseExceptionInterface
{
    /**
     * @param int $expectedCellCount
     * @param int $givenCellCount
     */
    public function __construct($expectedCellCount, $givenCellCount)
    {
        $message = sprintf(
            'Expected table cell count [%d] but got cell count [%d]',
            $expectedCellCount, $givenCellCount
        );

        parent::__construct($message);
    }
}