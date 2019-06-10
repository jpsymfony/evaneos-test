<?php

class Destination
{
    protected $id;
    protected $countryName;
    protected $conjunction;
    protected $name;
    protected $computerName;

    public function __construct(int $id, string $countryName, string $conjunction, string $computerName)
    {
        $this->id = $id;
        $this->countryName = $countryName;
        $this->conjunction = $conjunction;
        $this->computerName = $computerName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCountryName(): string
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     * @return Destination
     */
    public function setCountryName(string $countryName): Destination
    {
        $this->countryName = $countryName;
        return $this;
    }

    /**
     * @return string
     */
    public function getConjunction(): string
    {
        return $this->conjunction;
    }

    /**
     * @param string $conjunction
     * @return Destination
     */
    public function setConjunction(string $conjunction): Destination
    {
        $this->conjunction = $conjunction;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Destination
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getComputerName(): string
    {
        return $this->computerName;
    }

    /**
     * @param string $computerName
     * @return Destination
     */
    public function setComputerName(string $computerName): Destination
    {
        $this->computerName = $computerName;
        return $this;
    }
}
