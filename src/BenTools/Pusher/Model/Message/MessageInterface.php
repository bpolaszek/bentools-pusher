<?php

namespace BenTools\Pusher\Model\Message;

use BenTools\Pusher\Model\Handler\PushHandlerInterface;

interface MessageInterface {

    /**
     * Get Text.
     *
     * @return string
     */
    public function getText() : string;

    /**
     * @param PushHandlerInterface $handler
     *
     * @return array
     */
    public function getParamsFor(PushHandlerInterface $handler) : array;

    /**
     * @param PushHandlerInterface $handler
     * @param string               $param
     *
     * @return mixed|null
     */
    public function getParamFor(PushHandlerInterface $handler, $param);

    /**
     * @param PushHandlerInterface $handler
     * @param array                $params
     *
     * @return $this
     */
    public function setParamsFor(PushHandlerInterface $handler, array $params = []) : MessageInterface;

    /**
     * @return int
     */
    public function getTTL() : int;

    /**
     * @return string
     */
    public function __toString() : string;
}