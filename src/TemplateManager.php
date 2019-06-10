<?php

require_once __DIR__ . '/../src/TemplateBuilder.php';

class TemplateManager extends TemplateBuilder
{
    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

    public function __construct()
    {
        $this->applicationContext = ApplicationContext::getInstance();
    }

    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $tpl->setSubject($this->build($tpl->getSubject(), $data));
        $tpl->setContent($this->build($tpl->getContent(), $data));

        return $tpl;
    }
}
