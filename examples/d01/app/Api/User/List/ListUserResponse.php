<?php

declare(strict_types = 1);

namespace App\Api\User\List;

use Contributte\FrameX\Http\EntityListResponse;

final class ListUserResponse extends EntityListResponse
{

    public static function of(ListUserResult $result): self
	{
		$self = self::create();

		$payload = [];

		foreach ($result->entities as $entity) {
			$payload[] = $entity->toArray();
		}

		$self->entities = $payload;
		$self->count = $result->count;
		$self->limit = $result->limit;
		$self->page = $result->page;

		return $self;
	}

}
