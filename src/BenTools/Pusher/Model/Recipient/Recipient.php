<?php

namespace BenTools\Pusher\Model\Recipient;

use BenTools\Pusher\Model\Handler\PushHandlerInterface;
use function GuzzleHttp\json_decode;

class Recipient implements RecipientInterface {

    protected $identifier;
    protected $endpoint;
    protected $authKey;
    protected $authSecret;
    protected $options = [];

    /**
     * @inheritDoc
     */
    public function getIdentifier() : string {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier(string $identifier) : RecipientInterface {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEndpoint(): string {
        return $this->endpoint;
    }

    /**
     * @inheritDoc
     */
    public function setEndpoint(string $endpoint): RecipientInterface {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthKey(): ?string {
        return $this->authKey;
    }

    /**
     * @inheritDoc
     */
    public function setAuthKey(?string $authKey): RecipientInterface {
        $this->authKey = $authKey;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthSecret(): ?string {
        return $this->authSecret;
    }

    /**
     * @inheritDoc
     */
    public function setAuthSecret(?string $authSecret): RecipientInterface {
        $this->authSecret = $authSecret;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOption($key) {
        return $this->options[$key] ?? null;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setOption($key, $value) {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * @param string|array $json
     *
     * @return RecipientInterface
     */
    public static function unwrapJSON($json): self {
        if (is_string($json)) {
            $json = json_decode($json, true);
        }
        $recipient = new static;
        $recipient->setEndpoint($json['endpoint']);
        $recipient->setIdentifier(array_reverse(explode('/', $json['endpoint']))[0]);
        $recipient->setAuthKey($json['keys'][static::AUTH_KEY]);
        $recipient->setAuthSecret($json['keys'][static::AUTH_SECRET]);
        return $recipient;
    }

}