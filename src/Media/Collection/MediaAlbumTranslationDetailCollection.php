<?php declare(strict_types=1);

namespace Shopware\Media\Collection;

use Shopware\Media\Struct\MediaAlbumTranslationDetailStruct;
use Shopware\Shop\Collection\ShopBasicCollection;

class MediaAlbumTranslationDetailCollection extends MediaAlbumTranslationBasicCollection
{
    /**
     * @var MediaAlbumTranslationDetailStruct[]
     */
    protected $elements = [];

    public function getMediaAlbum(): MediaAlbumBasicCollection
    {
        return new MediaAlbumBasicCollection(
            $this->fmap(function (MediaAlbumTranslationDetailStruct $mediaAlbumTranslation) {
                return $mediaAlbumTranslation->getMediaAlbum();
            })
        );
    }

    public function getLanguages(): ShopBasicCollection
    {
        return new ShopBasicCollection(
            $this->fmap(function (MediaAlbumTranslationDetailStruct $mediaAlbumTranslation) {
                return $mediaAlbumTranslation->getLanguage();
            })
        );
    }

    protected function getExpectedClass(): string
    {
        return MediaAlbumTranslationDetailStruct::class;
    }
}
