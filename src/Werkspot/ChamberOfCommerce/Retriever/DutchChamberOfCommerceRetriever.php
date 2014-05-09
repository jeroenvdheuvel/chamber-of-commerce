<?php

namespace Werkspot\Component\ChamberOfCommerce\Retriever;

use Guzzle\Http\ClientInterface;
use Symfony\Component\DomCrawler\Crawler;
use Werkspot\Component\ChamberOfCommerce\Model\ChamberOfCommerce;

class DutchChamberOfCommerceRetriever implements ChamberOfCommerceRetriever
{
    /**
     * @var \Guzzle\Http\ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    /**
     * @param ClientInterface $client
     * @param string $url
     */
    public function __construct(ClientInterface $client, $url)
    {
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function find($chamberOfCommerceNumber)
    {
        $response = $this->client->get($this->url . $chamberOfCommerceNumber)->send();

        // TODO: Check status code

        $crawler = new Crawler($response->getBody(true));
        $chamberOfCommerceTableRows = $crawler->filter('table:nth-child(2) tr');

        $fetchedName = null;
        $fetchedChamberOfCommerceNumber = null;
        $fetchedInternetAddress = null;
        $fetchedStreetName = null;
        $fetchedHouseNumber = null;
        $fetchedHouseNumberAddition = null;
        $fetchedZipCode = null;
        $fetchedCity = null;

        foreach ($chamberOfCommerceTableRows as $row) {
            $crawledRow = new Crawler($row);
            $cells = $crawledRow->filter('td');

            $propertyName = $cells->getNode(0)->nodeValue;
            $propertyValue = $cells->getNode(1)->nodeValue;

            $propertyName = strtolower(str_replace(':', '', $propertyName));

            switch ($propertyName) {
                case 'naam':
                    $fetchedName = $propertyValue;
                    break;
                case 'kvk-nummer':
                    $fetchedChamberOfCommerceNumber = $propertyValue;
                    break;
                case 'internetadres':
                    $fetchedInternetAddress = $propertyValue;
                    break;
                case 'vestigingsadres':
                    list($fetchedStreetName, $fetchedHouseNumber, $fetchedHouseNumberAddition) = explode("\n", $propertyValue);
                    $fetchedStreetName = $this->trim($fetchedStreetName);
                    $fetchedHouseNumber = $this->trim($fetchedHouseNumber);
                    $fetchedHouseNumberAddition = $this->trim($fetchedHouseNumberAddition);
                    break;
                case 'vestigingsplaats':
                    list($fetchedZipCode, $fetchedCity) = explode("\n", $propertyValue);
                    $fetchedZipCode = $this->trim($fetchedZipCode);
                    $fetchedCity = $this->trim($fetchedCity);
            }
        }

        return new ChamberOfCommerce($fetchedChamberOfCommerceNumber, $fetchedName, $fetchedZipCode, $fetchedCity, $fetchedStreetName, $fetchedHouseNumber, $fetchedHouseNumberAddition, $fetchedInternetAddress);
    }

    /**
     * @param $str
     * @return string
     */
    private function trim($str)
    {
        return trim($str, chr(0xC2).chr(0xA0));
    }
}