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

class GoogleCloudMessagingHandler implements PushHandlerInterface {

    const IDENTIFIER  = 'gcm';
    const URL_PATTERN = 'https://android.googleapis.com/gcm/send/%s';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * Mandatory: Google API Key
     * @var string
     * @see https://console.developers.google.com/apis/dashboard
     */
    private $apiKey;

    /**
     * Optionnal: Google Sender Id - useful if you have multiple sender ids
     * @var string
     */
    private $senderId;

    /**
     * GoogleCloudMessagingHandler constructor.
     *
     * @param ClientInterface $client
     * @param null $apiKey
     * @param null $senderId
     */
    public function __construct(ClientInterface $client, $apiKey = null, $senderId = null) {
        $this->client   = $client;
        $this->apiKey   = $apiKey;
        $this->senderId = $senderId;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): string {
        return self::IDENTIFIER;
    }

    /**
     * @param MessageInterface $message
     * @param array $recipientIdentifiers
     *
     * @return RequestInterface
     * @throws \RuntimeException
     */
    private function prepareRequest(MessageInterface $message, RecipientInterface $recipient) {
        if (!$this->getApiKey()) {
            throw new \RuntimeException("No API Key provided.");
        }

        $uri = new Uri($recipient->getEndpoint() ?? sprintf(static::URL_PATTERN, $recipient->getIdentifier()));
        return Pusher::createStandardRequest($message, $recipient)
            ->withUri($uri)
            ->withHeader('Authorization', sprintf('key=%s', $this->getApiKey()));
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

    /**
     * @return string
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     *
     * @return $this - Provides Fluent Interface
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSenderId() {
        return $this->senderId;
    }

    /**
     * @param string|null $senderId
     * @return $this - Provides Fluent Interface
     */
    public function setSenderId($senderId) {
        $this->senderId = $senderId;
        return $this;
    }
}