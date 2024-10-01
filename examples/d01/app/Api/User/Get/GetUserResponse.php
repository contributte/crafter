<?php

declare(strict_types = 1);

namespace App\Api\User\Get;

use Contributte\FrameX\Http\EntityResponse;

final class GetUserResponse extends EntityResponse
{

    public static function of(UserDto $dto): self
    {
        $self = self::create();
        $self->payload = $dto;

        return $self;
    }

}
