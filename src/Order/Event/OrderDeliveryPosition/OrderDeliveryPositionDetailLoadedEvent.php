<?php declare(strict_types=1);

namespace Shopware\Order\Event\OrderDeliveryPosition;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;
use Shopware\Order\Collection\OrderDeliveryPositionDetailCollection;
use Shopware\Order\Event\OrderDelivery\OrderDeliveryBasicLoadedEvent;
use Shopware\Order\Event\OrderLineItem\OrderLineItemBasicLoadedEvent;

class OrderDeliveryPositionDetailLoadedEvent extends NestedEvent
{
    const NAME = 'order_delivery_position.detail.loaded';

    /**
     * @var TranslationContext
     */
    protected $context;

    /**
     * @var OrderDeliveryPositionDetailCollection
     */
    protected $orderDeliveryPositions;

    public function __construct(OrderDeliveryPositionDetailCollection $orderDeliveryPositions, TranslationContext $context)
    {
        $this->context = $context;
        $this->orderDeliveryPositions = $orderDeliveryPositions;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->context;
    }

    public function getOrderDeliveryPositions(): OrderDeliveryPositionDetailCollection
    {
        return $this->orderDeliveryPositions;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->orderDeliveryPositions->getOrderDeliveries()->count() > 0) {
            $events[] = new OrderDeliveryBasicLoadedEvent($this->orderDeliveryPositions->getOrderDeliveries(), $this->context);
        }
        if ($this->orderDeliveryPositions->getOrderLineItems()->count() > 0) {
            $events[] = new OrderLineItemBasicLoadedEvent($this->orderDeliveryPositions->getOrderLineItems(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}
