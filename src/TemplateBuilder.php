<?php

abstract class TemplateBuilder
{
    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

    public function __construct()
    {
        $this->applicationContext = ApplicationContext::getInstance();
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
        $this->hydrateTextWithUser($text, $data);

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
     * @param string $text
     * @param array $data
     */
    abstract protected function hydrateTextWithUser(string &$text, array $data): void;
}