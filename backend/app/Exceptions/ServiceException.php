<?php

namespace App\Exceptions;

use RuntimeException;

class ServiceException extends RuntimeException {
    protected int $status;

    protected array $payload;

    public function __construct(string $message, int $status = 400, array $payload = []) {
        parent::__construct($message, $status);

        $this->status = $status;
        $this->payload = $payload ?: ['error' => $message];
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function getPayload() {
        return $this->payload;
    }
}
