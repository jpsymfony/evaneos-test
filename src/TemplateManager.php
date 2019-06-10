<?php

require_once __DIR__ . '/../src/TemplateBuilder.php';

class TemplateManager extends TemplateBuilder
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

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
            $quoteFromRepository = $this->quoteRepository->getById($quote->id);
            $destinationOfQuote = $this->destinationRepository->getById($quote->destinationId);
            $usefulObject = $this->siteRepository->getById($quote->siteId);

            $this->fillSummaryHtml($text, $quoteFromRepository);
            $this->fillSummary($text, $quoteFromRepository);

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]', $destinationOfQuote->countryName, $text);
        }

        if (strpos($text, '[quote:destination_link]') !== false) {
            $text = str_replace('[quote:destination_link]', $usefulObject->url . '/' . $destinationOfQuote->countryName . '/quote/' . $quoteFromRepository->id, $text);
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
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
