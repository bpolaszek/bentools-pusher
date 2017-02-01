<?php

namespace BenTools\Pusher\Model\Push;

use BenTools\Pusher\Model\Message\MessageInterface;
use BenTools\Pusher\Model\Recipient\RecipientInterface;

interface PushInterface {

	const STATUS_PENDING = 'pending';
	const STATUS_DONE    = 'done';

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
	 * @return RecipientInterface[]
	 */
	public function getRecipients(): iterable;

	/**
	 * @return iterable|RecipientInterface[]
	 */
	public function getFailedRecipients(): iterable;

}