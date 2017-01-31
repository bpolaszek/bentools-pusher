<?php

namespace BenTools\Pusher\Model\Recipient;

interface RecipientCollectionInterface extends \IteratorAggregate {

    /**
     * @param RecipientInterface $recipient
     * @return $this
     */
    public function addRecipient(RecipientInterface $recipient) : RecipientCollectionInterface;

    /**
     * @param RecipientInterface $recipient
     * @return $this
     */
    public function removeRecipient(RecipientInterface $recipient) : RecipientCollectionInterface;

    /**
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function hasRecipient(RecipientInterface $recipient) : bool;

    /**
     * @return \Traversable|\Generator|RecipientInterface[]
     */
    public function getIterator();

}