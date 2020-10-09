<?php

declare(strict_types=1);

namespace App\Command;

use App\Serializer\JsonSerializer;
use App\YoutubeClient\Client;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @Command
 */
class YoutubeVideoInfoCommand extends HyperfCommand
{
    private Client $client;
    private JsonSerializer $serializer;

    public function __construct(Client $client, JsonSerializer $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;

        parent::__construct('youtube:video-info');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Youtube video info');
        $this->addArgument('id', InputArgument::REQUIRED, 'Video ID');
    }

    public function handle()
    {
        $id = $this->input->getArgument('id');
        $info = $this->client->getVideoInfo($id);

        echo $this->serializer->serialize($info) . PHP_EOL;
    }
}
