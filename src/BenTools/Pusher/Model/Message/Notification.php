<?php

namespace BenTools\Pusher\Model\Message;

use BenTools\Pusher\Model\Message\Notification\Action;

/**
 * A Notification object.
 * @see https://developer.mozilla.org/en/docs/Web/API/ServiceWorkerRegistration/showNotification
 */
class Notification extends AbstractMessage implements MessageInterface, \JsonSerializable {

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $body = '';

    /**
     * @var string
     */
    protected $icon = '';

    /**
     * @var string
     */
    protected $link = '';

    /**
     * @var string
     */
    protected $tag = '';

    /**
     * @var array
     */
    protected $actions = [];

    /**
     * @var string
     */
    protected $badge;

    /**
     * @var bool
     */
    protected $requireInteraction = false;

    /**
     * @var bool
     */
    protected $renotify = false;

    /**
     * @var null
     */
    protected $vibrate;

    /**
     * @var null
     */
    protected $lang;

    /**
     * @var string
     */
    protected $dir = 'auto';

    /**
     * Notification constructor.
     *
     * @param string $title
     * @param string $body
     * @param string $icon
     * @param string $badge
     * @param string $link
     * @param string $tag
     * @param bool $requireInteraction
     * @param bool $renotify
     * @param null $vibrate
     * @param null $lang
     * @param string $dir
     * @param array $actions
     */
    public function __construct(
        $title = '',
        $body = '',
        $icon = '',
        $badge = '',
        $link = '',
        $tag = '',
        $requireInteraction = false,
        $renotify = false,
        $vibrate = null,
        $lang = null,
        $dir = 'auto',
        array $actions = []
    ) {
        $this->title              = $title;
        $this->body               = $body;
        $this->icon               = $icon;
        $this->badge              = $badge;
        $this->link               = $link;
        $this->tag                = $tag;
        $this->requireInteraction = $requireInteraction;
        $this->renotify           = $renotify;
        $this->vibrate            = $vibrate;
        $this->lang               = $lang;
        $this->dir                = $dir;
        $this->actions            = $actions;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this - Provides Fluent Interface
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return $this - Provides Fluent Interface
     */
    public function setBody($body) {
        $this->body = $body;

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
     *
     * @return $this - Provides Fluent Interface
     */
    public function setIcon($icon) {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * @param string $tag
     *
     * @return $this - Provides Fluent Interface
     */
    public function setTag($tag) {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return $this - Provides Fluent Interface
     */
    public function setLink($link) {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getBadge() {
        return $this->badge;
    }

    /**
     * @param string $badge
     *
     * @return $this - Provides Fluent Interface
     */
    public function setBadge($badge) {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInteractionRequired() {
        return $this->requireInteraction;
    }

    /**
     * @param bool $requireInteraction
     *
     * @return $this - Provides Fluent Interface
     */
    public function setRequireInteraction($requireInteraction) {
        $this->requireInteraction = $requireInteraction;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldRenotify() {
        return $this->renotify;
    }

    /**
     * @param bool $renotify
     *
     * @return $this - Provides Fluent Interface
     */
    public function setRenotify($renotify) {
        $this->renotify = $renotify;

        return $this;
    }

    /**
     * @return null
     */
    public function getVibrate() {
        return $this->vibrate;
    }

    /**
     * @param null $vibrate
     *
     * @return $this - Provides Fluent Interface
     */
    public function setVibrate($vibrate) {
        $this->vibrate = $vibrate;

        return $this;
    }

    /**
     * @return null
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * @param null $lang
     *
     * @return $this - Provides Fluent Interface
     */
    public function setLang($lang) {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getDir() {
        return $this->dir;
    }

    /**
     * @param string $dir
     *
     * @return $this - Provides Fluent Interface
     */
    public function setDir($dir) {
        $this->dir = $dir;

        return $this;
    }

    /**
     * @return array
     */
    public function getActions() {
        return $this->actions;
    }

    /**
     * @param array $actions
     *
     * @return $this - Provides Fluent Interface
     */
    public function setActions(array $actions) {
        foreach ($actions AS $action) {
            $this->addAction($action);
        }

        return $this;
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action) {
        $this->actions[(string) $action] = $action;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        $data = [
            'title'              => $this->getTitle(),
            'body'               => $this->getBody(),
            'icon'               => $this->getIcon(),
            'tag'                => $this->getTag(),
            'data'               => [
                'link' => $this->getLink(),
            ],
            'actions'            => $this->getActions(),
            'requireInteraction' => $this->isInteractionRequired(),
            'renotify'           => $this->shouldRenotify(),
            'vibrate'            => $this->getVibrate(),
            'lang'               => $this->getLang(),
            'dir'                => $this->getDir(),
        ];

        $data = array_filter($data, function ($value) {
            return null !== $value;
        });

        return [
            'notification' => $data,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getText(): string {
        return json_encode($this);
    }

}