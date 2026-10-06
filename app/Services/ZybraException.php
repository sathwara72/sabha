<?php

namespace App\Services;

use RuntimeException;

class ZybraException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 0)
    {
        parent::__construct($message, $status);
    }

    /**
     * Whether the same request may be resent with the same Idempotency-Key.
     * Zybra stores 2xx/4xx responses against the key, so after a 4xx the
     * corrected request needs a new key; 5xx, 429, in-flight 409 and network
     * failures are safe to retry with the same key.
     */
    public function isRetryableWithSameKey(): bool
    {
        return $this->status === 0 || $this->status >= 500 || in_array($this->status, [409, 429], true);
    }
}
