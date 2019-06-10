<?php

class Quote
{
    protected $id;
    protected $siteId;
    protected $destinationId;
    protected $dateQuoted;

    public function __construct(int $id, int $siteId, int $destinationId, DateTime $dateQuoted)
    {
        $this->id = $id;
        $this->siteId = $siteId;
        $this->destinationId = $destinationId;
        $this->dateQuoted = $dateQuoted;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->siteId;
    }

    /**
     * @param int $siteId
     * @return Quote
     */
    public function setSiteId(int $siteId): Quote
    {
        $this->siteId = $siteId;
        return $this;
    }

    /**
     * @return int
     */
    public function getDestinationId(): int
    {
        return $this->destinationId;
    }

    /**
     * @param int $destinationId
     * @return Quote
     */
    public function setDestinationId(int $destinationId): Quote
    {
        $this->destinationId = $destinationId;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateQuoted(): DateTime
    {
        return $this->dateQuoted;
    }

    /**
     * @param DateTime $dateQuoted
     * @return Quote
     */
    public function setDateQuoted(DateTime $dateQuoted): Quote
    {
        $this->dateQuoted = $dateQuoted;
        return $this;
    }

    public static function renderHtml(Quote $quote)
    {
        return '<p>' . $quote->id . '</p>';
    }

    public static function renderText(Quote $quote)
    {
        return (string) $quote->id;
    }
}