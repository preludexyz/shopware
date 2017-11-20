<?php declare(strict_types=1);

namespace Shopware\Unit\Collection;

use Shopware\Api\Entity\EntityCollection;
use Shopware\Unit\Struct\UnitBasicStruct;

class UnitBasicCollection extends EntityCollection
{
    /**
     * @var UnitBasicStruct[]
     */
    protected $elements = [];

    public function get(string $uuid): ? UnitBasicStruct
    {
        return parent::get($uuid);
    }

    public function current(): UnitBasicStruct
    {
        return parent::current();
    }

    protected function getExpectedClass(): string
    {
        return UnitBasicStruct::class;
    }
}
