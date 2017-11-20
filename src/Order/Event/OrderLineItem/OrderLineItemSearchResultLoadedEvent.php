<?php declare(strict_types=1);

namespace Shopware\Order\Event\OrderLineItem;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Order\Struct\OrderLineItemSearchResult;

class OrderLineItemSearchResultLoadedEvent extends NestedEvent
{
    const NAME = 'order_line_item.search.result.loaded';

    /**
     * @var OrderLineItemSearchResult
     */
    protected $result;

    public function __construct(OrderLineItemSearchResult $result)
    {
        $this->result = $result;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->result->getContext();
    }
}
