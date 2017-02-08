<?php

namespace BenTools\Pusher\Model\Push;

use BenTools\Pusher\Model\Handler\PushHandlerInterface;
use BenTools\Pusher\Model\Message\MessageInterface;
use BenTools\Pusher\Model\Recipient\RecipientInterface;

interface PushInterface {

	const STATUS_PENDING = 'pending';
	const STATUS_DONE    = 'done';

    /**
     * @param RecipientInterface $recipient
     * @param PushHandlerInterface $pushHandler
     * @return $this|Push
     */
    public function addRecipient(RecipientInterface $recipient, PushHandlerInterface $pushHandler): PushInterface;

    /**
     * @param RecipientInterface $recipient
     * @return bool
     */
    public function hasRecipient(RecipientInterface $recipient): bool;

    /**
     * @return iterable|RecipientInterface[]
     */
    public function getRecipients(): iterable;

    /**
     * @param PushHandlerInterface $pushHandler
     * @return iterable|RecipientInterface[]
     */
    public function getRecipientsFor(PushHandlerInterface $pushHandler): iterable;

    /**
     * @return iterable|PushHandlerInterface[]
     */
    public function getHandlers(): iterable;

    /**
     * @param RecipientInterface $recipient
     * @return PushHandlerInterface
     * @throws \InvalidArgumentException
     */
    public function getHandlerFor(RecipientInterface $recipient): PushHandlerInterface;

	/**
	 * @return bool
	 */
	public function isPending(): bool;

	/**
	 * @return bool
	 */
	public function isDone(): bool;

	/**
	 * @param $status
	 *
	 * @return $this
	 */
	public function setStatus($status): PushInterface;

	/**
	 * @return bool
	 */
	public function hasErrors(): bool;

	/**
	 * @param RecipientInterface $recipient
	 * @param string             $reason
	 *
	 * @return $this
	 */
	public function setFailedFor(RecipientInterface $recipient, string $reason): PushInterface;

	/**
	 * @param RecipientInterface $recipient
	 *
	 * @return bool
	 */
	public function hasFailed(RecipientInterface $recipient): bool;

	/**
	 * @param RecipientInterface $recipient
	 *
	 * @return string
	 */
	public function getFailureReason(RecipientInterface $recipient): string;

	/**
	 * @return MessageInterface
	 */
	public function getMessage(): MessageInterface;

	/**
	 * @param MessageInterface $message
	 *
	 * @return $this
	 */
	public function setMessage(MessageInterface $message): PushInterface;

	/**
	 * @return iterable|RecipientInterface[]
	 */
	public function getFailedRecipients(): iterable;

}