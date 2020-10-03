<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\User;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use HyperfExt\Hashing\Contract\HashInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * @Command
 */
class GenUserCommand extends HyperfCommand
{
    private HashInterface $hasher;

    public function __construct(HashInterface $hasher)
    {
        $this->hasher = $hasher;

        parent::__construct('gen:user');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Create user');
        $this->addOption('password', 'p', InputOption::VALUE_OPTIONAL, 'User password', 't00r');
    }

    public function handle()
    {
        $user = new User();
        $user->setPassword($this->hasher->make($this->input->getOption('password')));
        $user->save();

        $this->line("User #{$user->getId()} created.", 'info');
    }
}
