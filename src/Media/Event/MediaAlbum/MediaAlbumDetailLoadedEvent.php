<?php declare(strict_types=1);

namespace Shopware\Media\Event\MediaAlbum;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;
use Shopware\Media\Collection\MediaAlbumDetailCollection;
use Shopware\Media\Event\Media\MediaBasicLoadedEvent;
use Shopware\Media\Event\MediaAlbumTranslation\MediaAlbumTranslationBasicLoadedEvent;

class MediaAlbumDetailLoadedEvent extends NestedEvent
{
    const NAME = 'media_album.detail.loaded';

    /**
     * @var TranslationContext
     */
    protected $context;

    /**
     * @var MediaAlbumDetailCollection
     */
    protected $mediaAlbum;

    public function __construct(MediaAlbumDetailCollection $mediaAlbum, TranslationContext $context)
    {
        $this->context = $context;
        $this->mediaAlbum = $mediaAlbum;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->context;
    }

    public function getMediaAlbum(): MediaAlbumDetailCollection
    {
        return $this->mediaAlbum;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->mediaAlbum->getParents()->count() > 0) {
            $events[] = new MediaAlbumBasicLoadedEvent($this->mediaAlbum->getParents(), $this->context);
        }
        if ($this->mediaAlbum->getMedia()->count() > 0) {
            $events[] = new MediaBasicLoadedEvent($this->mediaAlbum->getMedia(), $this->context);
        }
        if ($this->mediaAlbum->getTranslations()->count() > 0) {
            $events[] = new MediaAlbumTranslationBasicLoadedEvent($this->mediaAlbum->getTranslations(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}
