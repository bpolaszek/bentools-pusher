<?php

namespace BenTools\Pusher\Model\Message;

interface MessageInterface {

    /**
     * Get Text.
     *
     * @return string
     */
    public function getText(): string;

    /**
     * @return int
     */
    public function getTTL(): int;

    /**
     * @return string
     */
    public function __toString(): string;
}