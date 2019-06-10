<?php

abstract class TemplateBuilder
{
    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var DestinationRepository
     */
    protected $destinationRepository;

    /**
     * @var SiteRepository
     */
    protected $siteRepository;

    public function __construct()
    {
        $this->applicationContext = ApplicationContext::getInstance();
        $this->quoteRepository = QuoteRepository::getInstance();
        $this->destinationRepository = DestinationRepository::getInstance();
        $this->siteRepository = SiteRepository::getInstance();
    }

    /**
     * @param string $text
     * @param array $data
     *
     * @return string
     */
    public function build(string $text, array $data): string
    {
        $quote = $this->getQuote($data);
        $this->hydrateTextWithQuote($text, $quote);

        $user = $this->getUser($data);
        $this->hydrateTextWithUser($text, $user);

        return $text;
    }

    /**
     * @param array $data
     *
     * @return Quote|null
     */
    abstract protected function getQuote(array $data): ?Quote;

    /**
     * @param string $text
     * @param Quote|null $quote
     */
    abstract protected function hydrateTextWithQuote(string &$text, ?Quote $quote = null): void;

    /**
     * @param array $data
     *
     * @return User
     */
    abstract protected function getUser(array $data): User;

    /**
     * @param string $text
     * @param User $user
     */
    abstract protected function hydrateTextWithUser(string &$text, User $user): void;
}