<?php

namespace Werkspot\Component\ChamberOfCommerce\Tests\Retriever;

use PHPUnit_Framework_TestCase;
use Mockery;
use Guzzle\Service\ClientInterface;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Werkspot\Component\ChamberOfCommerce\Model\ChamberOfCommerce;
use Werkspot\Component\ChamberOfCommerce\Retriever\DutchChamberOfCommerceRetriever;

class DutchChamberOfCommerceRetrieverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getValidChamberOfCommerceNumbers
     *
     * @param string|int $number
     */
    public function testFindWithValidChamberOfCommerceNumber($number, $name, $zipCode, $city, $streetName, $houseNumber, $internetAddress)
    {
        $retriever = new DutchChamberOfCommerceRetriever($this->getHttpClient(), $this->getUrl());
        $givenChamberOfCommerce = $retriever->find($number);

        $expectedChamberOfCommerce = new ChamberOfCommerce($number, $name, $zipCode, $city, $streetName, $houseNumber, null, $internetAddress);

        $this->assertEquals($expectedChamberOfCommerce, $givenChamberOfCommerce);
    }

    public function testWithExpiredChamberOfCommerceNUmber()
    {

    }

    /**
     * @return array
     */
    public function getValidChamberOfCommerceNumbers()
    {
        return array(
            array('18079951', 'Werkspot B.V.', '1017BS', 'Amsterdam', 'Herengracht', 469, 'www.werkspot.nl'),
            array('24304157', 'Qweb Internet Services', '3115JC', 'Schiedam', 'Piet Heinstraat', 7, 'www.servers.nl'),
            array('05079434', 'Ping-Ping Ommen B.V.', '7731DB', 'Ommen', 'Markt', '32', null),
        );
    }

    /**
     * @return ClientInterface
     */
    private function getHttpClient()
    {
        $client = Mockery::mock('Guzzle\Http\ClientInterface');

        foreach ($this->getValidChamberOfCommerceNumbers() as $data) {
            $chamberOfCommerceNumber = $data[0];

            $request = $this->getHttpRequestForChamberOfCommerceNumber($chamberOfCommerceNumber);
            $client->shouldReceive('get')->with($this->getUrl() . $chamberOfCommerceNumber)->andReturn($request);
        }

        return $client;
    }

    /**
     * @param string $chamberOfCommerceNumber
     * @return RequestInterface
     */
    private function getHttpRequestForChamberOfCommerceNumber($chamberOfCommerceNumber)
    {
        $response = $this->getHttpResponseForChamberOfCommerceNumber($chamberOfCommerceNumber);

        $request = Mockery::mock('Guzzle\Http\Message\RequestInterface');
        $request->shouldReceive('send')->andReturn($response);

        return $request;
    }

    /**
     * @param string $chamberOfCommerceNumber
     * @return Response
     */
    private function getHttpResponseForChamberOfCommerceNumber($chamberOfCommerceNumber)
    {
        $data = $this->getResponseDataForChamberOfCommerceNumber($chamberOfCommerceNumber);

        $response = Mockery::mock('Guzzle\Http\Message\Response');
        $response->shouldReceive('getBody')->with(true)->andReturn($data);

        return $response;
    }

    /**
     * @param string $chamberOfCommerceNumber
     * @return string
     */
    private function getResponseDataForChamberOfCommerceNumber($chamberOfCommerceNumber)
    {
        return file_get_contents(dirname(__FILE__) . '/../data/dutch-chamber-of-commerce/kvk.nl/' . $chamberOfCommerceNumber . '.html');
    }

    /**
     * @return string
     */
    private function getUrl()
    {
        return 'https://server.db.kvk.nl/TST-BIN/FU/TSWS001@?BUTT=';
    }
}