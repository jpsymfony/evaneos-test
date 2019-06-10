<?php

require_once __DIR__ . '/../src/TemplateBuilder.php';

class TemplateManager extends TemplateBuilder
{
    /**
     * @param Template $tpl
     * @param array $data
     *
     * @return Template
     */
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $tpl->setSubject($this->build($tpl->getSubject(), $data));
        $tpl->setContent($this->build($tpl->getContent(), $data));

        return $tpl;
    }

    /**
     * @inheritDoc
     */
    protected function getQuote(array $data): ?Quote
    {
        return (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;
    }

    /**
     * @inheritDoc
     */
    protected function hydrateTextWithQuote(string &$text, ?Quote $quote = null): void
    {
        if ($quote) {
            $quoteFromRepository = $this->quoteRepository->getById($quote->getId());
            $destinationOfQuote = $this->destinationRepository->getById($quote->getDestinationId());
            $siteFromRepository = $this->siteRepository->getById($quote->getSiteId());

            $this->fillSummaryHtml($text, $quoteFromRepository);
            $this->fillSummary($text, $quoteFromRepository);
            $this->fillDestinationName($text, $destinationOfQuote);
            $this->fillDestinationLink($text, $siteFromRepository, $destinationOfQuote, $quoteFromRepository);
        }
    }

    /**
     * @param string $text
     * @param Quote $quote
     */
    protected function fillSummaryHtml(string &$text, Quote $quote): void
    {
        $text = str_replace('[quote:summary_html]', Quote::renderHtml($quote), $text);
    }

    /**
     * @param string $text
     * @param Quote $quote
     */
    protected function fillSummary(string &$text, Quote $quote): void
    {
        $text = str_replace('[quote:summary]', Quote::renderText($quote), $text);
    }

    /**
     * @param string $text
     * @param Destination $destination
     */
    protected function fillDestinationName(string &$text, Destination $destination): void
    {
        $text = str_replace('[quote:destination_name]', $destination->getCountryName(), $text);
    }

    /**
     * @param string $text
     * @param Site $site
     * @param Destination $destination
     * @param Quote $quote
     */
    protected function fillDestinationLink(string &$text, Site $site, Destination $destination, Quote $quote): void
    {
        $text = str_replace(
            '[quote:destination_link]',
            $site->getUrl() . '/' . $destination->getCountryName() . '/quote/' . $quote->getId(), $text
        );
    }

    /**
     * @inheritDoc
     */
    protected function hydrateTextWithUser(string &$text, User $user): void
    {
        $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->getFirstname())), $text);
    }

    /**
     * @inheritDoc
     */
    protected function getUser(array $data): User
    {
        return (isset($data['user']) and ($data['user'] instanceof User)) ? $data['user'] : $this->applicationContext->getCurrentUser();
    }
}
