<?php

declare(strict_types = 1);

namespace App\Api\User\List;

use App\Model\Api\Request\RequestFilter;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class ListUserRequestFilter extends RequestFilter
{

    public static function schema(): Schema
    {
        return RequestFilter::extend([
            'o' => Expect::structure([
                'username' => Expect::anyOf('asc', 'desc'),
            ])->required(false)->castTo('array'),
            'q' => Expect::structure([
                'username' => Expect::string(),
            ])->required(false)->castTo('array'),
        ])->castTo(self::class);
    }

}
