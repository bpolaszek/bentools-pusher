<?php

namespace BenTools\Pusher\Model\Recipient;

use BenTools\Pusher\Model\Handler\PushHandlerInterface;

interface RecipientInterface {

    const AUTH_KEY    = 'p256dh';
    const AUTH_SECRET = 'auth';

    /**
     * Returns the full endpoint URL,
     * i.e https://updates.push.services.mozilla.com/wpush/v1/gAAAAABYEhyddV1zx1zBpSZu8NlL2xrh78jlcAJKjYuyrStFBxqtnbu-pmlub3CieaqD_7cgSdj9ZRJWuvy3usRmcLCRcIMBZ8Bnlc1PzJ_nSOvHWU5S6oduNoRnOC_S3mpfxwmEa02O
     * @return null|string
     */
    public function getEndpoint(): ?string;

    /**
     * Returns the subscription identifier,
     * i.e gAAAAABYEhyddV1zx1zBpSZu8NlL2xrh78jlcAJKjYuyrStFBxqtnbu-pmlub3CieaqD_7cgSdj9ZRJWuvy3usRmcLCRcIMBZ8Bnlc1PzJ_nSOvHWU5S6oduNoRnOC_S3mpfxwmEa02O
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @return null|string
     */
    public function getAuthKey(): ?string;

    /**
     * @return null|string
     */
    public function getAuthSecret(): ?string;

    /**
     * Extra params
     * @param $key
     * @return mixed
     */
    public function getOption($key);

}