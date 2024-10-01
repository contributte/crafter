<?php

declare(strict_types = 1);

namespace App\Api\User\Delete;

use Contributte\FrameX\Http\DataResponse;

final class DeleteUserResponse extends DataResponse
{

    public static function of(): self
    {
        $self = self::create();

        return $self;
    }

}
