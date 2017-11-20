<?php declare(strict_types=1);

namespace Shopware\Customer\Event\CustomerGroupTranslation;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Customer\Struct\CustomerGroupTranslationSearchResult;
use Shopware\Framework\Event\NestedEvent;

class CustomerGroupTranslationSearchResultLoadedEvent extends NestedEvent
{
    const NAME = 'customer_group_translation.search.result.loaded';

    /**
     * @var CustomerGroupTranslationSearchResult
     */
    protected $result;

    public function __construct(CustomerGroupTranslationSearchResult $result)
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
