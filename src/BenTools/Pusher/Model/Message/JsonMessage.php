<?php

namespace BenTools\Pusher\Model\Message;

class JsonMessage extends AbstractMessage implements MessageInterface, \JsonSerializable  {

    /**
     * @var
     */
    private $data = [];

    public function __construct($data = null) {
        if (null !== $data) {
            $this->data = $data;
        }
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this - Provides Fluent Interface
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getText() : string {
        return json_encode($this);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return $this->data;
    }

}