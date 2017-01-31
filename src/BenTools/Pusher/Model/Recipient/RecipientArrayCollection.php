<?php

namespace BenTools\Pusher\Model\Recipient;

class RecipientArrayCollection implements RecipientCollectionInterface, \Countable {

    protected $recipients = [];

    /**
     * @inheritDoc
     */
    public function addRecipient(RecipientInterface $recipient) : RecipientCollectionInterface {
        $this->recipients[] = $recipient;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeRecipient(RecipientInterface $recipient) : RecipientCollectionInterface {
        foreach ($this AS $_recipient) {
            if ($recipient === $_recipient) {
                unset($recipient);
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasRecipient(RecipientInterface $recipient) : bool {
        return in_array($recipient, $this->recipients, true);
    }

    /**
     * @inheritDoc
     */
    public function getIterator() {
        foreach ($this->recipients AS $recipient) {
            yield $recipient;
        }
    }

    /**
     * @inheritDoc
     */
    public function count() {
        return count($this->recipients);
    }

}