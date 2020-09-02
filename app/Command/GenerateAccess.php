<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Hyperf\HttpServer\Router\DispatcherFactory;

/**
 * @Command
 */
class GenerateAccess extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('generate:access');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('生成路由命令');
    }

    public function handle()
    {
        $this->line('Hello Hyperf!', 'info');

        $a = new DispatcherFactory();
        $accesses = $a->getRouter('http')->getData();
        var_dump($accesses);
        var_dump($accesses[1]);
        var_dump($accesses[1]['GET'][0]['routeMap']);

    }
}
