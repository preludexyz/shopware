<?php declare(strict_types=1);

namespace Shopware\Mail\Event\MailAttachment;

use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Mail\Collection\MailAttachmentBasicCollection;

class MailAttachmentBasicLoadedEvent extends NestedEvent
{
    const NAME = 'mail_attachment.basic.loaded';

    /**
     * @var TranslationContext
     */
    protected $context;

    /**
     * @var MailAttachmentBasicCollection
     */
    protected $mailAttachments;

    public function __construct(MailAttachmentBasicCollection $mailAttachments, TranslationContext $context)
    {
        $this->context = $context;
        $this->mailAttachments = $mailAttachments;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->context;
    }

    public function getMailAttachments(): MailAttachmentBasicCollection
    {
        return $this->mailAttachments;
    }
}
