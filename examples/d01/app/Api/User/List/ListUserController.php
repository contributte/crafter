<?php

declare(strict_types = 1);

namespace App\Api\User\List;

use App\Api\AbstractController;
use App\Model\Api\Request\RequestFactory;
use Contributte\FrameX\Http\IResponse;
use Moderntv\Messenger\Bus\CommandBus;
use Psr\Http\Message\ServerRequestInterface;

final class ListUserController extends AbstractController
{

    public function __construct(
        private readonly CommandBus $bus,
        private readonly RequestFactory $requestFactory,
    )
    {
    }

    public function __invoke(ServerRequestInterface $serverRequest): IResponse
    {
        // TODO
    }

}
