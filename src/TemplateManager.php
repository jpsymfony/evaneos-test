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
}
