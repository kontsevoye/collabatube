<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Exception\Validator\ValidatorException;
use App\Exception\YoutubeClient\YoutubeClientException;
use App\Http\Request\PlaylistItem as PlaylistItemRequest;
use App\Http\Request\Room as RoomRequest;
use App\Middleware\ApiAuthenticateMiddleware;
use App\Model\Room;
use App\Model\User;
use App\PlaylistService\PlaylistItem;
use App\PlaylistService\RoomPlaylistService;
use App\Serializer\JsonSerializer;
use App\Validator\AnnotationValidator;
use App\YoutubeClient\Client;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\HttpMessage\Exception\UnauthorizedHttpException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use HyperfExt\Auth\Contracts\AuthManagerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @Controller(prefix="/api/v1/room")
 * @Middleware(ApiAuthenticateMiddleware::class)
 */
class RoomController extends AbstractController
{
    private User $user;

    private JsonSerializer $jsonSerializer;

    private AnnotationValidator $validator;

    private RoomPlaylistService $playlistService;

    public function __construct(
        AuthManagerInterface $authManager,
        JsonSerializer $jsonSerializer,
        AnnotationValidator $validator,
        RoomPlaylistService $playlistService
    ) {
        $user = $authManager->guard()->user();
        if (! ($user instanceof User)) {
            throw new UnauthorizedHttpException();
        }

        $this->user = $user;
        $this->jsonSerializer = $jsonSerializer;
        $this->validator = $validator;
        $this->playlistService = $playlistService;
    }

    /**
     * @RequestMapping(path="", methods="get")
     */
    public function list(ResponseInterface $response): PsrResponseInterface
    {
        return $response
            ->json(['rooms' => Room::all()->toArray()])
            ->withStatus(200);
    }

    /**
     * @RequestMapping(path="{id:\d+}", methods="get")
     */
    public function show(int $id, ResponseInterface $response): PsrResponseInterface
    {
        try {
            return $response
                ->json(['room' => Room::findOrFail($id)->toArray()])
                ->withStatus(200);
        } catch (ModelNotFoundException $e) {
            return $response
                ->json(['room' => null])
                ->withStatus(404);
        }
    }

    /**
     * @RequestMapping(path="", methods="post")
     */
    public function create(RequestInterface $request, ResponseInterface $response): PsrResponseInterface
    {
        /** @var RoomRequest $roomRequest */
        $roomRequest = $this->jsonSerializer->deserialize(
            $request->getBody()->getContents(),
            RoomRequest::class
        );
        try {
            $this->validator->validate($roomRequest);
        } catch (ValidatorException $e) {
            return $response
                ->json([
                    'message' => $e->getMessage(),
                ])
                ->withStatus(400);
        }
        $room = Room::fromRequest($roomRequest);
        $room->save();

        return $response
            ->json([
                'room' => $room->toArray(),
            ])
            ->withStatus(200);
    }

    /**
     * @RequestMapping(path="{id:\d+}", methods="put")
     */
    public function update(int $id, RequestInterface $request, ResponseInterface $response): PsrResponseInterface
    {
        try {
            $room = Room::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $response
                ->json(['room' => null])
                ->withStatus(404);
        }

        /** @var RoomRequest $roomRequest */
        $roomRequest = $this->jsonSerializer->deserialize(
            $request->getBody()->getContents(),
            RoomRequest::class
        );
        try {
            $this->validator->validate($roomRequest);
        } catch (ValidatorException $e) {
            return $response
                ->json([
                    'message' => $e->getMessage(),
                ])
                ->withStatus(400);
        }
        $room->mergeWithRequest($roomRequest);
        $room->save();

        return $response
            ->json(['room' => $room->toArray()])
            ->withStatus(200);
    }

    /**
     * @RequestMapping(path="{id:\d+}/playlist", methods="get")
     */
    public function getPlaylist(int $id, ResponseInterface $response): PsrResponseInterface
    {
        try {
            $room = Room::findOrFail($id);

            return $response
                ->json([
                    'room' => $room->toArray(),
                    'playlist' => $this->jsonSerializer->normalize($this->playlistService->list($room)),
                ])
                ->withStatus(200);
        } catch (ModelNotFoundException $e) {
            return $response
                ->json(['room' => null])
                ->withStatus(404);
        }
    }

    /**
     * @RequestMapping(path="{id:\d+}/playlist", methods="post")
     */
    public function addPlaylistItem(
        int $id,
        RequestInterface $request,
        ResponseInterface $response,
        Client $ytClient
    ): PsrResponseInterface {
        try {
            $room = Room::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $response
                ->json(['room' => null])
                ->withStatus(404);
        }
        /** @var PlaylistItemRequest $playlistItemRequest */
        $playlistItemRequest = $this->jsonSerializer->deserialize(
            $request->getBody()->getContents(),
            PlaylistItemRequest::class
        );
        try {
            $this->validator->validate($playlistItemRequest);
        } catch (ValidatorException $e) {
            return $response
                ->json([
                    'message' => $e->getMessage(),
                ])
                ->withStatus(400);
        }
        try {
            $info = $ytClient->getVideoInfo($playlistItemRequest->getUrl());
        } catch (YoutubeClientException $e) {
            return $response
                ->json([
                    'message' => $e->getMessage(),
                ])
                ->withStatus(400);
        }
        $pItem = PlaylistItem::fromVideoItem($info);

        return $response
            ->json([
                'room' => $room->toArray(),
                'length' => $this->playlistService->add($room, $pItem),
                'pItem' => $this->jsonSerializer->normalize($pItem)
            ])
            ->withStatus(200);
    }
}
