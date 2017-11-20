<?php declare(strict_types=1);

namespace Shopware\Tax\Event\Tax;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Tax\Collection\TaxBasicCollection;

class TaxBasicLoadedEvent extends NestedEvent
{
    const NAME = 'tax.basic.loaded';

    /**
     * @var TranslationContext
     */
    protected $context;

    /**
     * @var TaxBasicCollection
     */
    protected $taxes;

    public function __construct(TaxBasicCollection $taxes, TranslationContext $context)
    {
        $this->context = $context;
        $this->taxes = $taxes;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->context;
    }

    public function getTaxes(): TaxBasicCollection
    {
        return $this->taxes;
    }
}
