<?php

namespace BenTools\Pusher\Model\Message;

abstract class AbstractMessage implements MessageInterface {

    protected $TTL = 0;

    /**
     * @return int
     */
    public function getTTL(): int {
        return $this->TTL;
    }

    /**
     * @param int $TTL
     * @return $this - Provides Fluent Interface
     */
    public function setTTL(int $TTL) {
        $this->TTL = $TTL;
        return $this;
    }

    /**
     * @inheritDoc
     */
    final public function __toString(): string {
        return $this->getText();
    }
}