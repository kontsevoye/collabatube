<?php

namespace App\YoutubeClient;

use App\Exception\YoutubeClient\VideoNotFoundException;
use App\Exception\YoutubeClient\YoutubeClientException;
use App\Serializer\ListDenormalizer;
use App\Serializer\JsonSerializer;
use App\YoutubeClient\Response\VideoInfoResponse;
use App\YoutubeClient\Response\VideoItem;
use App\YoutubeClient\Response\VideoItemList;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\HttpMessage\Uri\Uri;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;

class Client
{
    private \GuzzleHttp\Client $client;
    private JsonSerializer $serializer;
    private string $apiKey;

    public function __construct(
        ClientFactory $clientFactory,
        JsonSerializer $serializer,
        ConfigInterface $config,
        string $baseUri = 'https://www.googleapis.com/youtube/v3/',
        float $timeout = 10.0
    ) {
        $this->client = $clientFactory->create([
            'base_uri' => $baseUri,
            'timeout'  => $timeout,
        ]);
        $this->serializer = $serializer;
        $this->apiKey = $config->get('youtube_data_api_key');
    }

    private function parseVideoId(string $id): string
    {
        try {
            new Uri($id);
        } catch (\InvalidArgumentException $e) {
            return $id;
        }

        $re = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)(?<id>[^"&?\/\s]{11})/i';
        preg_match($re, $id, $matches);

        if (!array_key_exists('id', $matches)) {
            return $id;
        }

        return $matches['id'];
    }

    /**
     * @param string $id Video id or url
     * @return VideoItem
     * @throws VideoNotFoundException
     * @throws YoutubeClientException
     */
    public function getVideoInfo(string $id): VideoItem
    {
        try {
            $r = $this->client->get(
                "videos?part=contentDetails&part=snippet&id={$this->parseVideoId($id)}&key={$this->apiKey}",
                [
                    RequestOptions::HEADERS => [
                        'Accept' => 'application/json',
                    ],
                ],
            );
        } catch (BadResponseException $e) {
            if ($e->getCode() === 404) {
                throw new VideoNotFoundException($e->getMessage(), $e->getCode(), $e);
            } else {
                throw new YoutubeClientException($e->getMessage(), $e->getCode(), $e);
            }
        } catch (\Throwable $e) {
            throw new YoutubeClientException($e->getMessage(), $e->getCode(), $e);
        }

        /** @var VideoInfoResponse $videoInfo */
        $videoInfo = $this->serializer->deserialize(
            (string) $r->getBody()->getContents(),
            VideoInfoResponse::class,
            [
                DateIntervalNormalizer::FORMAT_KEY => 'PT%iM%sS',
                ListDenormalizer::ASSOCIATION_CONTEXT_KEY => [
                    VideoItemList::class => VideoItem::class,
                ],
            ]
        );

        if ($videoInfo->getPageInfo()->getTotalResults() !== 1) {
            throw new VideoNotFoundException(
                "Video list size is not eq 1, but {$videoInfo->getPageInfo()->getTotalResults()}"
            );
        }

        return $videoInfo->getItemList()->getFirstItem();
    }
}
