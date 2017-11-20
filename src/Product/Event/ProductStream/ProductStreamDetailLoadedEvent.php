<?php declare(strict_types=1);

namespace Shopware\Product\Event\ProductStream;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;
use Shopware\Listing\Event\ListingSorting\ListingSortingBasicLoadedEvent;
use Shopware\Product\Collection\ProductStreamDetailCollection;
use Shopware\Product\Event\Product\ProductBasicLoadedEvent;

class ProductStreamDetailLoadedEvent extends NestedEvent
{
    const NAME = 'product_stream.detail.loaded';

    /**
     * @var TranslationContext
     */
    protected $context;

    /**
     * @var ProductStreamDetailCollection
     */
    protected $productStreams;

    public function __construct(ProductStreamDetailCollection $productStreams, TranslationContext $context)
    {
        $this->context = $context;
        $this->productStreams = $productStreams;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->context;
    }

    public function getProductStreams(): ProductStreamDetailCollection
    {
        return $this->productStreams;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->productStreams->getListingSortings()->count() > 0) {
            $events[] = new ListingSortingBasicLoadedEvent($this->productStreams->getListingSortings(), $this->context);
        }
        if ($this->productStreams->getAllProductTabs()->count() > 0) {
            $events[] = new ProductBasicLoadedEvent($this->productStreams->getAllProductTabs(), $this->context);
        }
        if ($this->productStreams->getAllProducts()->count() > 0) {
            $events[] = new ProductBasicLoadedEvent($this->productStreams->getAllProducts(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}
