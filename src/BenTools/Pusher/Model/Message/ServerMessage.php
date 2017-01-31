<?php

namespace BenTools\Pusher\Model\Message;

class ServerMessage extends AbstractMessage implements MessageInterface, \JsonSerializable {

    protected $data;

    /**
     * ServerMessage constructor.
     * @param $data
     */
    public function __construct($data = null) {
        $this->data = $data;
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
        return [
            'serverMessage' => $this->data,
        ];
    }

}