<?php

namespace BenTools\Pusher\Model\Push;

use BenTools\Pusher\Model\Message\MessageInterface;
use BenTools\Pusher\Model\Recipient\RecipientArrayCollection;
use BenTools\Pusher\Model\Recipient\RecipientCollectionInterface;
use BenTools\Pusher\Model\Recipient\RecipientInterface;

class Push implements PushInterface, RecipientCollectionInterface {

    protected $message;
    protected $recipients;
    protected $status   = self::STATUS_PENDING;
    protected $failures = [];

    /**
     * Push constructor.
     * @param RecipientCollectionInterface|null $recipientCollection
     */
    public function __construct(RecipientCollectionInterface $recipientCollection = null) {
        $this->recipients = $recipientCollection ?: new RecipientArrayCollection();
    }

    /**
     * @inheritDoc
     */
    public function getMessage() : MessageInterface {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function setMessage(MessageInterface $message) : PushInterface {
        $this->message = $message;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRecipients() : RecipientCollectionInterface {
        return $this->recipients;
    }

    /**
     * @inheritDoc
     */
    public function setRecipients(RecipientCollectionInterface $recipients) : PushInterface {
        $this->recipients = $recipients;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPending() : bool {
        return static::STATUS_PENDING === $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isDone() : bool {
        return static::STATUS_DONE === $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status) : PushInterface {
        $this->status = $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasErrors() : bool {
        return count($this->failures) > 0;
    }

    /**
     * @inheritDoc
     */
    public function setFailedFor(RecipientInterface $recipient, string $reason) : PushInterface {
        $this->failures[$recipient->getIdentifier()] = [
            'recipient' => $recipient,
            'reason'    => $reason,
        ];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasFailed(RecipientInterface $recipient) : bool {
        return array_key_exists($recipient->getIdentifier(), $this->failures);
    }

    /**
     * @inheritDoc
     */
    public function getFailureReason(RecipientInterface $recipient) : string {
        return isset($this->failures[$recipient->getIdentifier()]) ? $this->failures[$recipient->getIdentifier()]['reason'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getFailedRecipients() : RecipientCollectionInterface {
        $recipients = array_map(function ($failure) {
            return $failure['recipient'];
        }, $this->failures);
        $collection = new RecipientArrayCollection();
        foreach ($recipients AS $recipient) {
            $collection->addRecipient($recipient);
        }
        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function addRecipient(RecipientInterface $recipient) : RecipientCollectionInterface {
        $this->getRecipients()->addRecipient($recipient);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeRecipient(RecipientInterface $recipient) : RecipientCollectionInterface {
        $this->getRecipients()->removeRecipient($recipient);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasRecipient(RecipientInterface $recipient) : bool {
        return $this->getRecipients()->hasRecipient($recipient);
    }

    /**
     * @inheritDoc
     */
    public function getIterator() {
        return $this->getRecipients()->getIterator();
    }

}