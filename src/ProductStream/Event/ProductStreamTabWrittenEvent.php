<?php declare(strict_types=1);

namespace Shopware\ProductStream\Event;

use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;

class ProductStreamTabWrittenEvent extends NestedEvent
{
    const NAME = 'product_stream_tab.written';

    /**
     * @var string[]
     */
    private $productStreamTabUuids;

    /**
     * @var NestedEventCollection
     */
    private $events;

    /**
     * @var array
     */
    private $errors;

    public function __construct(array $productStreamTabUuids, array $errors = [])
    {
        $this->productStreamTabUuids = $productStreamTabUuids;
        $this->events = new NestedEventCollection();
        $this->errors = $errors;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return string[]
     */
    public function getProductStreamTabUuids(): array
    {
        return $this->productStreamTabUuids;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function addEvent(NestedEvent $event): void
    {
        $this->events->add($event);
    }

    public function getEvents(): NestedEventCollection
    {
        return $this->events;
    }
}
