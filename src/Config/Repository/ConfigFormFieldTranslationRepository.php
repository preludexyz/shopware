<?php declare(strict_types=1);

namespace Shopware\Config\Repository;

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
use Shopware\Config\Collection\ConfigFormFieldTranslationBasicCollection;
use Shopware\Config\Collection\ConfigFormFieldTranslationDetailCollection;
use Shopware\Config\Definition\ConfigFormFieldTranslationDefinition;
use Shopware\Config\Event\ConfigFormFieldTranslation\ConfigFormFieldTranslationAggregationResultLoadedEvent;
use Shopware\Config\Event\ConfigFormFieldTranslation\ConfigFormFieldTranslationBasicLoadedEvent;
use Shopware\Config\Event\ConfigFormFieldTranslation\ConfigFormFieldTranslationDetailLoadedEvent;
use Shopware\Config\Event\ConfigFormFieldTranslation\ConfigFormFieldTranslationSearchResultLoadedEvent;
use Shopware\Config\Event\ConfigFormFieldTranslation\ConfigFormFieldTranslationUuidSearchResultLoadedEvent;
use Shopware\Config\Struct\ConfigFormFieldTranslationSearchResult;
use Shopware\Context\Struct\TranslationContext;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ConfigFormFieldTranslationRepository implements RepositoryInterface
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

    public function search(Criteria $criteria, TranslationContext $context): ConfigFormFieldTranslationSearchResult
    {
        $uuids = $this->searchUuids($criteria, $context);

        $entities = $this->readBasic($uuids->getUuids(), $context);

        $aggregations = null;
        if ($criteria->getAggregations()) {
            $aggregations = $this->aggregate($criteria, $context);
        }

        $result = ConfigFormFieldTranslationSearchResult::createFromResults($uuids, $entities, $aggregations);

        $event = new ConfigFormFieldTranslationSearchResultLoadedEvent($result);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $result;
    }

    public function aggregate(Criteria $criteria, TranslationContext $context): AggregationResult
    {
        $result = $this->aggregator->aggregate(ConfigFormFieldTranslationDefinition::class, $criteria, $context);

        $event = new ConfigFormFieldTranslationAggregationResultLoadedEvent($result);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $result;
    }

    public function searchUuids(Criteria $criteria, TranslationContext $context): UuidSearchResult
    {
        $result = $this->searcher->search(ConfigFormFieldTranslationDefinition::class, $criteria, $context);

        $event = new ConfigFormFieldTranslationUuidSearchResultLoadedEvent($result);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $result;
    }

    public function readBasic(array $uuids, TranslationContext $context): ConfigFormFieldTranslationBasicCollection
    {
        /** @var ConfigFormFieldTranslationBasicCollection $entities */
        $entities = $this->reader->readBasic(ConfigFormFieldTranslationDefinition::class, $uuids, $context);

        $event = new ConfigFormFieldTranslationBasicLoadedEvent($entities, $context);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $entities;
    }

    public function readDetail(array $uuids, TranslationContext $context): ConfigFormFieldTranslationDetailCollection
    {
        /** @var ConfigFormFieldTranslationDetailCollection $entities */
        $entities = $this->reader->readDetail(ConfigFormFieldTranslationDefinition::class, $uuids, $context);

        $event = new ConfigFormFieldTranslationDetailLoadedEvent($entities, $context);
        $this->eventDispatcher->dispatch($event->getName(), $event);

        return $entities;
    }

    public function update(array $data, TranslationContext $context): GenericWrittenEvent
    {
        $affected = $this->writer->update(ConfigFormFieldTranslationDefinition::class, $data, WriteContext::createFromTranslationContext($context));
        $event = GenericWrittenEvent::createFromWriterResult($affected, $context, []);
        $this->eventDispatcher->dispatch(GenericWrittenEvent::NAME, $event);

        return $event;
    }

    public function upsert(array $data, TranslationContext $context): GenericWrittenEvent
    {
        $affected = $this->writer->upsert(ConfigFormFieldTranslationDefinition::class, $data, WriteContext::createFromTranslationContext($context));
        $event = GenericWrittenEvent::createFromWriterResult($affected, $context, []);
        $this->eventDispatcher->dispatch(GenericWrittenEvent::NAME, $event);

        return $event;
    }

    public function create(array $data, TranslationContext $context): GenericWrittenEvent
    {
        $affected = $this->writer->insert(ConfigFormFieldTranslationDefinition::class, $data, WriteContext::createFromTranslationContext($context));
        $event = GenericWrittenEvent::createFromWriterResult($affected, $context, []);
        $this->eventDispatcher->dispatch(GenericWrittenEvent::NAME, $event);

        return $event;
    }
}
