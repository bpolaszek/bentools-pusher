<?php

namespace BenTools\Pusher;

use BenTools\Pusher\Model\Message\MessageInterface;
use BenTools\Pusher\Model\Push\PushInterface;
use BenTools\Pusher\Model\Recipient\RecipientInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Minishlink\WebPush\Encryption;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Promise\all;

class Pusher {

    /**
     * @param PushInterface $push
     */
    public function push(PushInterface $push) {
        $this->pushAsync($push)->wait();
    }

    /**
     * @param PushInterface $push
     *
     * @return PromiseInterface
     */
    public function pushAsync(PushInterface $push): PromiseInterface {
        return all($this->getPromises($push))->then(function () use ($push) {
            $push->setStatus(PushInterface::STATUS_DONE);
        })->otherwise(function () use ($push) {
            $push->setStatus(PushInterface::STATUS_DONE);
        });
    }

    /**
     * @param PushInterface $push
     *
     * @return \Generator
     */
    private function getPromises(PushInterface $push) {
        foreach ($push->getHandlers() AS $handler) {
            yield $handler->getIdentifier() => $handler->getPromise($push);
        }
    }

    /**
     * @param MessageInterface   $message
     * @param RecipientInterface $recipient
     *
     * @return RequestInterface
     */
    public static function createStandardRequest(MessageInterface $message, RecipientInterface $recipient): RequestInterface {
        $request = new Request('POST', '');
        $request = $request->withHeader('TTL', (int) $message->getTTL());

        if ($message->getText()) {

            $encrypted = Encryption::encrypt(Encryption::padPayload($message->getText(), true), $recipient->getAuthKey(), $recipient->getAuthSecret(), false);

            $headers = [
                'Content-Length'   => strlen($encrypted['cipherText']),
                'Content-Type'     => 'application/octet-stream',
                'Content-Encoding' => 'aesgcm',
                'Encryption'       => 'keyid="p256dh";salt="' . $encrypted['salt'] . '"',
                'Crypto-Key'       => 'keyid="p256dh";dh="' . $encrypted['localPublicKey'] . '"',
            ];
            foreach ($headers AS $key => $value) {
                $request = $request->withHeader($key, $value);
            }

            $request = $request->withBody(\GuzzleHttp\Psr7\stream_for($encrypted['cipherText']));
        }

        return $request;
    }
}