<?php

namespace Werkspot\Component\ChamberOfCommerce\Model;

class ChamberOfCommerce
{
    /** @var string|int */
    private $number;

    /** @var string */
    private $countryCode;

    /** @var string */
    private $name;

    /** @var string */
    private $streetName;

    /** @var string|int */
    private $houseNumber;

    /** @var string */
    private $houseNumberAddition;

    /** @var string */
    private $zipCode;

    /** @var string */
    private $city;

    /** @var string */
    private $internetAddress;

    public function __construct($number, $name, $zipCode, $city, $streetName, $houseNumber, $houseNumberAddition, $internetAddress)
    {
        $this->number = $number;
        $this->name = $name;
        $this->zipCode = $zipCode;
        $this->city = $city;
        $this->streetName = $streetName;
        $this->houseNumber = $houseNumber;
        $this->houseNumberAddition = $houseNumberAddition;
        $this->internetAddress = $internetAddress;
    }

    /**
     * @return string|int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @return int|string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * @return string
     */
    public function getHouseNumberAddition()
    {
        return $this->houseNumberAddition;
    }

    /**
     * @return string
     */
    public function getInternetAddress()
    {
        return $this->internetAddress;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }
}