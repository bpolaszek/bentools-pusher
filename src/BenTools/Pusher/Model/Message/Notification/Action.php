<?php

namespace BenTools\Pusher\Model\Message\Notification;

class Action implements \JsonSerializable {

    protected $identifier;
    protected $title;
    protected $icon = '';

    /**
     * Action constructor.
     * @param        $identifier
     * @param        $title
     * @param string $icon
     */
    public function __construct($identifier = null, $title = null, $icon = '') {
        $this->identifier = $identifier;
        $this->title      = $title;
        $this->icon       = $icon;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return $this - Provides Fluent Interface
     */
    public function setIdentifier($identifier) {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this - Provides Fluent Interface
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this - Provides Fluent Interface
     */
    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString() {
        return $this->getIdentifier();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return [
            'action' => $this->getIdentifier(),
            'title'  => $this->getTitle(),
            'icon'   => $this->getIcon(),
        ];
    }
}