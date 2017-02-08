<?php

namespace BenTools\Pusher\Model\Handler;

use BenTools\Pusher\Model\Message\MessageInterface;
use BenTools\Pusher\Model\Push\PushInterface;
use BenTools\Pusher\Model\Recipient\Recipient;
use BenTools\Pusher\Model\Recipient\RecipientInterface;
use BenTools\Pusher\Pusher;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Promise\all;

class MozillaHandler implements PushHandlerInterface {

    const IDENTIFIER  = 'mozilla';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * MozillaHandler constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string {
        return self::IDENTIFIER;
    }

    /**
     * @param MessageInterface $message
     * @param RecipientInterface $recipient
     *
     * @return RequestInterface
     */
    private function prepareRequest(MessageInterface $message, RecipientInterface $recipient) {
        $uri = new Uri($recipient->getEndpoint());
        return Pusher::createStandardRequest($message, $recipient)->withUri($uri);
    }

    /**
     * @inheritDoc
     */
    public function getPromise(PushInterface $push): PromiseInterface {
        return all($this->getPromises($push));
    }

    /**
     * @param PushInterface $push
     *
     * @return \Generator
     */
    private function getPromises(PushInterface $push) {
        foreach ($push->getRecipientsFor($this) as $recipient) {
            yield $recipient->getIdentifier() => $this->createPromiseForPush($push, $recipient);
        }
    }

    /**
     * @param PushInterface $push
     * @param RecipientInterface $recipient
     *
     * @return PromiseInterface
     */
    private function createPromiseForPush(PushInterface $push, RecipientInterface $recipient): PromiseInterface {
        return $this->client->sendAsync($this->prepareRequest($push->getMessage(), $recipient))
            ->otherwise(function (\Exception $exception) use ($push, $recipient) {
                $push->setFailedFor($recipient, $exception);
            });
    }

}