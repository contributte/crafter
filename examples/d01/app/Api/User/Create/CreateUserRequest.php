<?php

declare(strict_types = 1);

namespace App\Api\User\Create;

final readonly class CreateUserRequest
{

    public function __construct(
        public CreateUserRequestBody $body,
    )
    {
    }

}
