<?php

namespace Werkspot\Component\ChamberOfCommerce\Retriever;

use Werkspot\Component\ChamberOfCommerce\Model\ChamberOfCommerceRecord;

interface ChamberOfCommerceRetriever
{
    /**
     * @param string|int $chamberOfCommerceNumber
     * @return ChamberOfCommerceRecord
     */
    public function find($chamberOfCommerceNumber);
}