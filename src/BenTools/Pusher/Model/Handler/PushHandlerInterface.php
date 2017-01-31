<?php

namespace BenTools\Pusher\Model\Handler;

use BenTools\Pusher\Model\Push\PushInterface;
use BenTools\Pusher\Model\Recipient\RecipientInterface;
use GuzzleHttp\Promise\PromiseInterface;

interface PushHandlerInterface {

    /**
     * @return string
     */
    public function getIdentifier() : string;

    /**
     * @param PushInterface $push
     * @return PromiseInterface
     */
    public function getPromise(PushInterface $push) : PromiseInterface;

    /**
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function supports(RecipientInterface $recipient) : bool;

}