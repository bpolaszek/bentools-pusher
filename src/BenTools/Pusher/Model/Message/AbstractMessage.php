<?php

namespace BenTools\Pusher\Model\Message;

use BenTools\Pusher\Model\Handler\PushHandlerInterface;

abstract class AbstractMessage implements MessageInterface {

    protected $params = [];
    protected $TTL    = 0;

    /**
     * @inheritDoc
     */
    public function getParamsFor(PushHandlerInterface $handler) : array {
        return isset($this->params[$handler->getIdentifier()]) ? $this->params[$handler->getIdentifier()] : [];
    }

    /**
     * @inheritDoc
     */
    public function getParamFor(PushHandlerInterface $handler, $param) {
        $params = $this->getParamsFor($handler);
        return isset($params[$param]) ? $params[$param] : null;
    }

    /**
     * @inheritDoc
     */
    public function setParamsFor(PushHandlerInterface $handler, array $params = []) : MessageInterface {
        $this->params[$handler->getIdentifier()] = $params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTTL() : int {
        return $this->TTL;
    }

    /**
     * @param mixed $TTL
     * @return $this - Provides Fluent Interface
     */
    public function setTTL(int $TTL) {
        $this->TTL = $TTL;
        return $this;
    }

    /**
     * @inheritDoc
     */
    final public function __toString() : string {
        return $this->getText();
    }
}