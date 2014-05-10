<?php

namespace Werkspot\Component\ChamberOfCommerce\Tests\Exception;

use PHPUnit_Framework_TestCase;
use stdClass;
use Werkspot\Component\ChamberOfCommerce\Exception\ChamberOfCommerceRecordException;

class ChamberOfCommerceRecordExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getChamberOfCommerceNumbers
     *
     * @param string|int $number
     */
    public function testChamberOfCommerceRecordException($number)
    {
        $e = new TestChamberOfCommerceRecordException($number);

        $this->assertSame($number, $e->getChamberOfCommerceNumber());
    }

    /**
     * @return array
     */
    public function getChamberOfCommerceNumbers()
    {
        return array(
            array('12346578'),
            array('00000000'),
            array('0'),
            array(12345678),
            array(0),
            array(null),
            array(array()),
            array(new stdClass()),
        );
    }
}

class TestChamberOfCommerceRecordException extends ChamberOfCommerceRecordException {}