<?php

namespace BenTools\Pusher\Model\Message;

class Ping extends AbstractMessage implements MessageInterface {

    /**
     * @inheritDoc
     */
    public function getText(): string {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function setText(string $text): MessageInterface {
        throw new \RuntimeException("A ping has no text.");
    }

}