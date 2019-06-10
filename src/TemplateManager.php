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
        if ($quote)
        {
            $_quoteFromRepository = QuoteRepository::getInstance()->getById($quote->id);
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->destinationId);
            $usefulObject = SiteRepository::getInstance()->getById($quote->siteId);

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($_quoteFromRepository),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($_quoteFromRepository),
                        $text
                    );
                }
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]',$destinationOfQuote->countryName,$text);
        }

        if (strpos($text, '[quote:destination_link]') !== false) {
            $text = str_replace('[quote:destination_link]', $usefulObject->url . '/' . $destinationOfQuote->countryName . '/quote/' . $_quoteFromRepository->id, $text);
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }
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
