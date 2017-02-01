<?php

namespace BenTools\Pusher\Model\Push;

use BenTools\Pusher\Model\Message\MessageInterface;
use BenTools\Pusher\Model\Recipient\RecipientInterface;

class Push implements PushInterface {

    /**
     * @var MessageInterface
     */
    protected $message;

    /**
     * @var RecipientInterface[]
     */
    protected $recipients = [];

    /**
     * @var string
     */
    protected $status     = self::STATUS_PENDING;

    /**
     * @var array
     */
    protected $failures   = [];

    /**
     * Push constructor.
     * @param $recipients
     */
    public function __construct($recipients = []) {
        $this->setRecipients($recipients);
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): MessageInterface {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function setMessage(MessageInterface $message): PushInterface {
        $this->message = $message;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRecipients(): iterable {
        return $this->recipients;
    }

    /**
     * @param iterable $recipients
     * @return PushInterface
     */
    public function setRecipients(iterable $recipients): self {
        $this->recipients = [];
        foreach ($recipients AS $recipient) {
            $this->addRecipient($recipient);
        }
        return $this;
    }

    /**
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function hasRecipient(RecipientInterface $recipient): bool {
        return in_array($recipient, $this->recipients);
    }

    /**
     * @param RecipientInterface $recipient
     * @return $this
     */
    public function addRecipient(RecipientInterface $recipient): self {
        if ($this->hasRecipient($recipient)) {
            throw new \InvalidArgumentException("This recipient is already part of that push - check with Push::hasRecipient() first");
        }
        $this->recipients[] = $recipient;
        return $this;
    }

    /**
     * @param RecipientInterface $recipient
     * @return $this
     */
    public function removeRecipient(RecipientInterface $recipient): self {
        if (false !== ($i = array_search($recipient, $this->recipients))) {
            unset($this->recipients[$i]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPending(): bool {
        return static::STATUS_PENDING === $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isDone(): bool {
        return static::STATUS_DONE === $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status): PushInterface {
        $this->status = $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasErrors(): bool {
        return count($this->failures) > 0;
    }

    /**
     * @inheritDoc
     */
    public function setFailedFor(RecipientInterface $recipient, string $reason): PushInterface {
        $this->failures[$recipient->getIdentifier()] = [
            'recipient' => $recipient,
            'reason'    => $reason,
        ];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasFailed(RecipientInterface $recipient): bool {
        return array_key_exists($recipient->getIdentifier(), $this->failures);
    }

    /**
     * @inheritDoc
     */
    public function getFailureReason(RecipientInterface $recipient): string {
        return isset($this->failures[$recipient->getIdentifier()]) ? $this->failures[$recipient->getIdentifier()]['reason'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getFailedRecipients(): iterable {
        $recipients = new \ArrayIterator(array_map(function ($failure) {
            return $failure['recipient'];
        }, $this->failures));
        return $recipients;
    }

    /**
     * @inheritDoc
     */
    public function getIterator() {
        return is_array($this->recipients) ? new \ArrayIterator($this->recipients) : $this->getRecipients();
    }

}