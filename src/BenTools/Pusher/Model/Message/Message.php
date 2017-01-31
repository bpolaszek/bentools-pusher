<?php

namespace BenTools\Pusher\Model\Message;

class Message extends AbstractMessage implements MessageInterface {

    protected $text   = '';

    /**
     * Message constructor.
     * @param string $text
     */
    public function __construct($text = '') {
        $this->text = $text;
    }

    /**
     * @inheritDoc
     */
    public function getText() : string {
        return $this->text;
    }

    /**
     * @inheritDoc
     */
    public function setText(string $text) : MessageInterface {
        $this->text = $text;
        return $this;
    }

}