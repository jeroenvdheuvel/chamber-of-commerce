<?php

namespace Werkspot\Component\ChamberOfCommerce\Tests\Retriever;

use PHPUnit_Framework_TestCase;
use Mockery;
use Guzzle\Service\ClientInterface;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Werkspot\Component\ChamberOfCommerce\Model\ChamberOfCommerceRecord;
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
        $retriever = new DutchChamberOfCommerceRetriever($this->getHttpClient($number), $this->getUrl());
        $givenChamberOfCommerce = $retriever->find($number);

        $expectedChamberOfCommerce = new ChamberOfCommerceRecord($number, $name, 'nl', $zipCode, $city, $streetName, $houseNumber, null, $internetAddress);

        $this->assertEquals($expectedChamberOfCommerce, $givenChamberOfCommerce);
    }

    /**
     * @expectedException \Werkspot\Component\ChamberOfCommerce\Exception\ServiceUnavailableException
     * @expectedExceptionMessage server.db.kvk.nl
     */
    public function testFindWithUnavailableService()
    {
        $number = '18079951';

        $retriever = new DutchChamberOfCommerceRetriever($this->getHttpClientThatThrowsExceptionOnSend($number), $this->getUrl());
        $retriever->find($number);
    }

    /**
     * @expectedException \Werkspot\Component\ChamberOfCommerce\Exception\NotFoundException
     * @expectedExceptionMessage 07079435
     */
    public function testFindNonexistentChamberOfCommerceNumber()
    {
        $number = '07079435';

        $retriever = new DutchChamberOfCommerceRetriever($this->getHttpClient($number), $this->getUrl());
        $retriever->find($number);
    }

    /**
     * @expectedException \Werkspot\Component\ChamberOfCommerce\Exception\UnsubscribedFromChamberOfCommerceException
     * @expectedExceptionMessage 05079435
     */
    public function testWithExpiredChamberOfCommerceNumber()
    {
        // TODO: Combine with the valid check
        $number = '05079435';

        $retriever = new DutchChamberOfCommerceRetriever($this->getHttpClient($number), $this->getUrl());
        $retriever->find($number);
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
            array('05079420-status-subscribed', 'Remington Haus B.V.', '8017JJ', 'Zwolle', 'Hanzelaan', '276', null),
        );
    }

    /**
     * @return ClientInterface
     */
    private function getHttpClient($chamberOfCommerceNumber)
    {
//        $c = new \Guzzle\Http\Client();
//        $this->client->setDefaultOption('timeout', 0.001);
//        $this->client->setDefaultOption('connect_timeout', 0.001);
        $client = Mockery::mock('Guzzle\Http\ClientInterface');

        $request = $this->getHttpRequestForChamberOfCommerceNumber($chamberOfCommerceNumber);
        $client->shouldReceive('get')->once()->with($this->getUrl() . $chamberOfCommerceNumber)->andReturn($request);

        return $client;
    }

    /**
     * @return ClientInterface
     */
    private function getHttpClientThatThrowsExceptionOnSend($chamberOfCommerceNumber)
    {
        $client = Mockery::mock('Guzzle\Http\ClientInterface');

        $request = $this->getHttpRequestForChamberOfCommerceNumberThatThrowsServiceCurlExceptionOnSend();
        $client->shouldReceive('get')->once()->with($this->getUrl() . $chamberOfCommerceNumber)->andReturn($request);

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
        $request->shouldReceive('send')->once()->withNoArgs()->andReturn($response);

        return $request;
    }

    /**
     * @return RequestInterface
     */
    private function getHttpRequestForChamberOfCommerceNumberThatThrowsServiceCurlExceptionOnSend()
    {
        $request = Mockery::mock('Guzzle\Http\Message\RequestInterface');
        $request->shouldReceive('send')->andThrow(new CurlException());
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
        $response->shouldReceive('getBody')->once()->with(true)->andReturn($data);

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