<?php

namespace BenTools\Pusher\Model\Push;

use BenTools\Pusher\Model\Handler\PushHandlerInterface;
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
     * @var PushHandlerInterface[]
     */
    protected $handlers = [];

    /**
     * @var string
     */
    protected $status = self::STATUS_PENDING;

    /**
     * @var array
     */
    protected $failures = [];

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
        $output = [];
        foreach ($this->recipients AS $pushHandlerHash => $recipients) {
            foreach ($recipients AS $recipient) {
                $output[] = $recipient;
            }
        }
        return $output;
    }

    /**
     * @inheritDoc
     */
    public function getRecipientsFor(PushHandlerInterface $pushHandler): iterable {
        $pushHandlerHash = spl_object_hash($pushHandler);
        if (!isset($this->handlers[$pushHandlerHash])) {
            throw new \InvalidArgumentException("This handler is not registered.");
        }
        return $this->recipients[$pushHandlerHash] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getHandlers(): iterable {
        if (empty($this->handlers)) {
            throw new \InvalidArgumentException("No handler registered.");
        }
        return $this->handlers;
    }

    /**
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function hasRecipient(RecipientInterface $recipient): bool {
        $exists = false;
        foreach ($this->recipients AS $pushHandlerHash => $recipients) {
            if (in_array($recipient, $this->recipients)) {
                $exists = true;
                break;
            }
        }
        return $exists;
    }

    /**
     * @param RecipientInterface $recipient
     * @param PushHandlerInterface $pushHandler
     * @return $this|Push
     */
    public function addRecipient(RecipientInterface $recipient, PushHandlerInterface $pushHandler): PushInterface {
        if ($this->hasRecipient($recipient)) {
            throw new \InvalidArgumentException("This recipient is already part of that push - check with Push::hasRecipient() first");
        }
        $pushHandlerHash = spl_object_hash($pushHandler);
        if (!isset($this->handlers[$pushHandlerHash])) {
            $this->handlers[$pushHandlerHash] = $pushHandler;
        }
        if (!isset($this->recipients[$pushHandlerHash])) {
            $this->recipients[$pushHandlerHash] = [$recipient];
        }
        else {
            $this->recipients[$pushHandlerHash][] = $recipient;
        }
        return $this;
    }

    /**
     * @param RecipientInterface $recipient
     * @return $this
     */
    public function removeRecipient(RecipientInterface $recipient): self {
        foreach ($this->recipients AS $pushHandlerHash => $recipients) {
            if (false !== ($i = array_search($recipient, $recipients))) {
                unset($this->recipients[$pushHandlerHash][$i]);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHandlerFor(RecipientInterface $recipient): PushHandlerInterface {
        $handler = null;
        foreach ($this->recipients AS $pushHandlerHash => $recipients) {
            foreach ($recipients AS $_recipient) {
                if ($_recipient === $recipient) {
                    $handler = $this->handlers[$pushHandlerHash];
                }
            }
        }
        if (!$handler instanceof PushHandlerInterface) {
            throw new \InvalidArgumentException("Unable to find handler for that recipient");
        }
        return $handler;
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