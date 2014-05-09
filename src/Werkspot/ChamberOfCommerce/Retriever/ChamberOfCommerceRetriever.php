<?php

namespace Werkspot\Component\ChamberOfCommerce\Retriever;

use Werkspot\Component\ChamberOfCommerce\Model\ChamberOfCommerce;

interface ChamberOfCommerceRetriever
{
    /**
     * @param string|int $chamberOfCommerceNumber
     * @return ChamberOfCommerce
     */
    public function find($chamberOfCommerceNumber);
}