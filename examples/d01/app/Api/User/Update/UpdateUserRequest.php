<?php

declare(strict_types = 1);

namespace App\Api\User\Update;

final readonly class UpdateUserRequest
{

    public function __construct(
        public UpdateUserRequestBody $body,
    )
    {
    }

}
