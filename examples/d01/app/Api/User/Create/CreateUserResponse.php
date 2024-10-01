<?php

declare(strict_types = 1);

namespace App\Api\User\Create;

use Contributte\FrameX\Http\EntityResponse;

final class CreateUserResponse extends EntityResponse
{

    public static function of(UserDto $dto): self
    {
        $self = self::create();
        $self->payload = $dto;

        return $self;
    }

}
