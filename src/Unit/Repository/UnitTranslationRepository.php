<?php declare(strict_types=1);

namespace Shopware\Unit\Repository;

use Shopware\Api\Read\EntityReaderInterface;
use Shopware\Api\RepositoryInterface;
use Shopware\Api\Search\AggregationResult;
use Shopware\Api\Search\Criteria;
use Shopware\Api\Search\EntityAggregatorInterface;
use Shopware\Api\Search\EntitySearcherInterface;
use Shopware\Api\Search\UuidSearchResult;
use Shopware\Api\Write\EntityWriterInterface;
use Shopware\Api\Write\GenericWrittenEvent;
use Shopware\Api\Write\WriteContext;
use Shopware\Context\Struct\TranslationContext;
use Shopware\Unit\Collection\UnitTranslationBasicCollection;
use Shopware\Unit\Collection\UnitTranslationDetailCollection;
use Shopware\Unit\Definition\UnitTranslationDefinition;
use Shopware\Unit\Event\UnitTranslation\UnitTranslationAggregationResultLoadedEvent;
use Shopware\Unit\Event\UnitTranslation\UnitTranslationBasicLoadedEvent;
use Shopware\Unit\Event\UnitTranslation\UnitTranslationDetailLoadedEvent;
use Shopware\Unit\Event\UnitTranslation\UnitTranslationSearchResultLoadedEvent;
use Shopware\Unit\Event\UnitTranslation\UnitTranslationUuidSearchResultLoadedEvent;
use Shopware\Unit\Struct\UnitTranslationSearchResult;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UnitTranslationRepository implements RepositoryInterface
{
    /**
     * @var EntityReaderInterface
     */
    private $reader;

    /**
     * @var EntityWriterInterface
     */
    private $writer;

    /**
     * @var EntitySearcherInterface
     */
    private $searcher;

    /**
     * @var EntityAggregatorInterface
     */
    private $aggregator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        EntityReaderInterface $reader,
        EntityWriterInterface $writer,
        EntitySearcherInterface $searcher,
        EntityAggregatorInterface $aggregator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->searcher = $searcher;
        $this->aggregator = $aggregator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function search(Criteria $criteria, TranslationContext $context): UnitTranslationSearchResult
    {
        $uuids = $this->searchUuids($criteria, $context);

        $entities = $this->readBasic($uuids->getUuids(), $context);

        $aggregations = null;
        if ($criteria->getAggregations()) {
            $aggregations = $this->aggregate($criteria, $context);
        }

        $result = UnitTranslationSearchResult::createFromResults($uuids, $entities, $aggregations);

        $event = new UnitTranslationSearchResultLoadedEvent($result);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $result;
    }

    public function aggregate(Criteria $criteria, TranslationContext $context): AggregationResult
    {
        $result = $this->aggregator->aggregate(UnitTranslationDefinition::class, $criteria, $context);

        $event = new UnitTranslationAggregationResultLoadedEvent($result);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $result;
    }

    public function searchUuids(Criteria $criteria, TranslationContext $context): UuidSearchResult
    {
        $result = $this->searcher->search(UnitTranslationDefinition::class, $criteria, $context);

        $event = new UnitTranslationUuidSearchResultLoadedEvent($result);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $result;
    }

    public function readBasic(array $uuids, TranslationContext $context): UnitTranslationBasicCollection
    {
        /** @var UnitTranslationBasicCollection $entities */
        $entities = $this->reader->readBasic(UnitTranslationDefinition::class, $uuids, $context);

        $event = new UnitTranslationBasicLoadedEvent($entities, $context);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $entities;
    }

    public function readDetail(array $uuids, TranslationContext $context): UnitTranslationDetailCollection
    {
        /** @var UnitTranslationDetailCollection $entities */
        $entities = $this->reader->readDetail(UnitTranslationDefinition::class, $uuids, $context);

        $event = new UnitTranslationDetailLoadedEvent($entities, $context);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $entities;
    }

    public function update(array $data, TranslationContext $context): GenericWrittenEvent
    {
        $affected = $this->writer->update(UnitTranslationDefinition::class, $data, WriteContext::createFromTranslationContext($context));
        $event = GenericWrittenEvent::createFromWriterResult($affected, $context, []);
        $this->eventDispatcher->dispatch(GenericWrittenEvent::NAME, $event);

        return $event;
    }

    public function upsert(array $data, TranslationContext $context): GenericWrittenEvent
    {
        $affected = $this->writer->upsert(UnitTranslationDefinition::class, $data, WriteContext::createFromTranslationContext($context));
        $event = GenericWrittenEvent::createFromWriterResult($affected, $context, []);
        $this->eventDispatcher->dispatch(GenericWrittenEvent::NAME, $event);

        return $event;
    }

    public function create(array $data, TranslationContext $context): GenericWrittenEvent
    {
        $affected = $this->writer->insert(UnitTranslationDefinition::class, $data, WriteContext::createFromTranslationContext($context));
        $event = GenericWrittenEvent::createFromWriterResult($affected, $context, []);
        $this->eventDispatcher->dispatch(GenericWrittenEvent::NAME, $event);

        return $event;
    }
}
