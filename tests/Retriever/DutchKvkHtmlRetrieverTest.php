<?php

namespace Werkspot\Component\ChamberOfCommerce\Tests\Retriever;

use PHPUnit_Framework_TestCase;
use Mockery;
use Guzzle\Service\ClientInterface;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Werkspot\Component\ChamberOfCommerce\Model\ChamberOfCommerceRecord;
use Werkspot\Component\ChamberOfCommerce\Retriever\DutchKvkHtmlRetriever;

class DutchKvkHtmlRetrieverTest extends PHPUnit_Framework_TestCase
{
    const URL = 'https://server.db.kvk.nl/TST-BIN/FU/TSWS001@?BUTT=';
    
    /**
     * @dataProvider getValidChamberOfCommerceNumbers
     *
     * @param string|int $number
     */
    public function testFindWithValidChamberOfCommerceNumber($number, $name, $zipCode, $city, $streetName, $houseNumber, $houseNumberAddition, $internetAddress)
    {
        $retriever = new DutchKvkHtmlRetriever($this->getHttpClient($number), self::URL);
        $givenChamberOfCommerce = $retriever->find($number);

        $expectedChamberOfCommerce = new ChamberOfCommerceRecord($number, $name, 'nl', $zipCode, $city, $streetName, $houseNumber, $houseNumberAddition, $internetAddress);

        $this->assertEquals($expectedChamberOfCommerce, $givenChamberOfCommerce);
    }

    /**
     * @return array
     */
    public function getValidChamberOfCommerceNumbers()
    {
        return array(
            array('18079951', 'Werkspot B.V.', '1017BS', 'Amsterdam', 'Herengracht', 469, null, 'www.werkspot.nl'),
            array('24304157', 'Qweb Internet Services', '3115JC', 'Schiedam', 'Piet Heinstraat', 7, null, 'www.servers.nl'),
            array('05079434', 'Ping-Ping Ommen B.V.', '7731DB', 'Ommen', 'Markt', '32', null, null),
            array('18080015', 'PARC polyester and rubber repair B.V.', '4251LA', 'Werkendam', 'Bruningsstraat', '21', '23', null),
            array('18080039', 'Stichting ARTV', '5021LL', 'Tilburg', 'Groenstraat', '139', '391', null),
            array('18080051', 'Tylo B.V.', '5038BC', 'Tilburg', 'Willem II-straat', '45', 'a', 'www.tylo.nl'),
            array('18079994', 'Orthopedische schoentechniek "de Biest" B.V.', '5084HT', 'Biest-Houtakker', 'Biestsestraat', '105', null, null),
        );
    }

    /**
     * @expectedException \Werkspot\Component\ChamberOfCommerce\Exception\ServiceUnavailableException
     * @expectedExceptionMessage server.db.kvk.nl
     */
    public function testFindWhileServiceIsUnavailable()
    {
        $number = 1;

        $retriever = new DutchKvkHtmlRetriever($this->getHttpClientThatThrowsExceptionOnSend($number), self::URL);
        $retriever->find($number);
    }

    /**
     * @dataProvider getNonExistingChamberOfCommerceNumbers
     *
     * @expectedException \Werkspot\Component\ChamberOfCommerce\Exception\NotFoundException
     */
    public function testFindNonexistentChamberOfCommerceNumber($number)
    {
        $retriever = new DutchKvkHtmlRetriever($this->getHttpClient($number), self::URL);
        $retriever->find($number);
    }

    /**
     * @return array
     */
    public function getNonExistingChamberOfCommerceNumbers()
    {
        return array(
            array('07079435'),
            array('08079951'),
            array('offline'),
        );
    }

    /**
     * @dataProvider getChamberOfCommerceNumbersWithInvalidStatus
     */
    public function testChamberOfCommerceNumberWithInvalidStatus($number, $status)
    {
        $expectedException = '\Werkspot\Component\ChamberOfCommerce\Exception\InvalidChamberOfCommerceStatusException';
        $expectedExceptionMessage = sprintf('number [%s] has invalid status [%s]', $number, $status);
        $this->setExpectedException($expectedException, $expectedExceptionMessage);

        $retriever = new DutchKvkHtmlRetriever($this->getHttpClient($number), self::URL);
        $retriever->find($number);
    }

    /**
     * @return array
     */
    public function getChamberOfCommerceNumbersWithInvalidStatus()
    {
        return array(
            array('05079420', 'Uitgeschreven uit het Handelsregister'),
            array('05079435', 'Uitgeschreven uit het Handelsregister'),
            array('18079980', 'Faillissement'),
        );
    }

    /**
     * @dataProvider getInvalidStatusCodes
     *
     * @param int $responseStatusCode
     */
    public function testFindThatReturnsInvalidResponseStatusCode($responseStatusCode)
    {
        $expectedException = '\Werkspot\Component\ChamberOfCommerce\Exception\UnexpectedHttpStatusCodeException';
        $expectedExceptionMessage = sprintf('Expected status code [%d] but got status code [%d]', 200, $responseStatusCode);
        $this->setExpectedException($expectedException, $expectedExceptionMessage);

        $number = '18079951';

        $retriever = new DutchKvkHtmlRetriever($this->getHttpClient($number, $responseStatusCode), self::URL);
        $retriever->find($number);
    }

    /**
     * @return array
     */
    public function getInvalidStatusCodes()
    {
        return array(
            array(201),
            array(301),
            array(302),
            array(400),
            array(404),
            array(500),
            array(501),
            array(503),
        );
    }

    /**
     * @dataProvider getChamberOfCommerceNumbersWithInvalidResponse
     *
     * @expectedException \Werkspot\Component\ChamberOfCommerce\Exception\InvalidChamberOfCommerceResponseException
     *
     * @param string|int $number
     */
    public function testFindWithInvalidResponse($number)
    {
        $retriever = new DutchKvkHtmlRetriever($this->getHttpClient($number), self::URL);
        $retriever->find($number);
    }

    /**
     * @return array
     */
    public function getChamberOfCommerceNumbersWithInvalidResponse()
    {
        return array(
            array('too-few-table-cells'),
            array('too-many-table-cells'),
        );
    }

    /**
     * @return ClientInterface
     */
    private function getHttpClient($chamberOfCommerceNumber, $responseStatusCode = 200)
    {
        $client = Mockery::mock('Guzzle\Http\ClientInterface');

        $request = $this->getHttpRequestForChamberOfCommerceNumber($chamberOfCommerceNumber, $responseStatusCode);
        $client->shouldReceive('get')->once()->with(self::URL . $chamberOfCommerceNumber)->andReturn($request);

        return $client;
    }

    /**
     * @return ClientInterface
     */
    private function getHttpClientThatThrowsExceptionOnSend($chamberOfCommerceNumber)
    {
        $client = Mockery::mock('Guzzle\Http\ClientInterface');

        $request = $this->getHttpRequestForChamberOfCommerceNumberThatThrowsServiceCurlExceptionOnSend();
        $client->shouldReceive('get')->once()->with(self::URL . $chamberOfCommerceNumber)->andReturn($request);

        return $client;
    }

    /**
     * @param string $chamberOfCommerceNumber
     * @param int $responseStatusCode
     * @return RequestInterface
     */
    private function getHttpRequestForChamberOfCommerceNumber($chamberOfCommerceNumber, $responseStatusCode = 200)
    {
        $response = $this->getHttpResponseForChamberOfCommerceNumber($chamberOfCommerceNumber, $responseStatusCode);

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
     * @param int $statusCode
     * @return Response
     */
    private function getHttpResponseForChamberOfCommerceNumber($chamberOfCommerceNumber, $statusCode = 200)
    {
        $data = $this->getResponseDataForChamberOfCommerceNumber($chamberOfCommerceNumber);

        $response = Mockery::mock('Guzzle\Http\Message\Response');

        $response->shouldReceive('getStatusCode')->between(1, 2)->withNoArgs()->andReturn($statusCode);

        $getBodyMethodCalls = $statusCode === 200 ? 2 : 0;
        $response->shouldReceive('getBody')->between(0, $getBodyMethodCalls)->with(true)->andReturn($data);

        $getEffectiveUrlMethodCalls = $statusCode === 200 ? 0 : 1;
        $response->shouldReceive('getEffectiveUrl')->times($getEffectiveUrlMethodCalls)->withNoArgs()->andReturn('www.werkspot.nl');

        return $response;
    }

    /**
     * @param string $chamberOfCommerceNumber
     * @return string
     */
    private function getResponseDataForChamberOfCommerceNumber($chamberOfCommerceNumber)
    {
        return file_get_contents(dirname(__FILE__) . '/../data/dutch-kvk-html/' . $chamberOfCommerceNumber . '.html');
    }
}