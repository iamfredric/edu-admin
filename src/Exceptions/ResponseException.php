<?php

namespace Iamfredric\EduAdmin\Exceptions;

use Exception;
use Throwable;

class ResponseException extends Exception
{
    /**
     * @param string|null $message
     * @param mixed[]|null $attributes
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        ?string $message = '',
        protected ?array $attributes = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message ?: '', $code, $previous);
    }

    /**
     * @return mixed[]|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }
}
