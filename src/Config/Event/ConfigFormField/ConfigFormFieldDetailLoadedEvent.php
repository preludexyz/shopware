<?php declare(strict_types=1);

namespace Shopware\Config\Event\ConfigFormField;

use Shopware\Config\Collection\ConfigFormFieldDetailCollection;
use Shopware\Config\Event\ConfigForm\ConfigFormBasicLoadedEvent;
use Shopware\Config\Event\ConfigFormFieldTranslation\ConfigFormFieldTranslationBasicLoadedEvent;
use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;

class ConfigFormFieldDetailLoadedEvent extends NestedEvent
{
    const NAME = 'config_form_field.detail.loaded';

    /**
     * @var TranslationContext
     */
    protected $context;

    /**
     * @var ConfigFormFieldDetailCollection
     */
    protected $configFormFields;

    public function __construct(ConfigFormFieldDetailCollection $configFormFields, TranslationContext $context)
    {
        $this->context = $context;
        $this->configFormFields = $configFormFields;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->context;
    }

    public function getConfigFormFields(): ConfigFormFieldDetailCollection
    {
        return $this->configFormFields;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->configFormFields->getConfigForms()->count() > 0) {
            $events[] = new ConfigFormBasicLoadedEvent($this->configFormFields->getConfigForms(), $this->context);
        }
        if ($this->configFormFields->getTranslations()->count() > 0) {
            $events[] = new ConfigFormFieldTranslationBasicLoadedEvent($this->configFormFields->getTranslations(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}
