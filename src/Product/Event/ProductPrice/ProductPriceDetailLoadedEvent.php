<?php declare(strict_types=1);

namespace Shopware\Product\Event\ProductPrice;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Customer\Event\CustomerGroup\CustomerGroupBasicLoadedEvent;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;
use Shopware\Product\Collection\ProductPriceDetailCollection;
use Shopware\Product\Event\Product\ProductBasicLoadedEvent;

class ProductPriceDetailLoadedEvent extends NestedEvent
{
    const NAME = 'product_price.detail.loaded';

    /**
     * @var TranslationContext
     */
    protected $context;

    /**
     * @var ProductPriceDetailCollection
     */
    protected $productPrices;

    public function __construct(ProductPriceDetailCollection $productPrices, TranslationContext $context)
    {
        $this->context = $context;
        $this->productPrices = $productPrices;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->context;
    }

    public function getProductPrices(): ProductPriceDetailCollection
    {
        return $this->productPrices;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->productPrices->getCustomerGroups()->count() > 0) {
            $events[] = new CustomerGroupBasicLoadedEvent($this->productPrices->getCustomerGroups(), $this->context);
        }
        if ($this->productPrices->getProducts()->count() > 0) {
            $events[] = new ProductBasicLoadedEvent($this->productPrices->getProducts(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}
