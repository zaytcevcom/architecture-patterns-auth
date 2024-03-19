<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function App\Components\env;

return [
    EntityManagerInterface::class => static function (ContainerInterface $container): EntityManager {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *     metadata_dirs:string[],
         *     dev_mode:bool,
         *     proxy_dir:string,
         *     cache_dir:string|null,
         *     types:array<string,class-string<Type>>,
         *     subscribers:string[],
         *     connection:array<string, mixed>
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: $settings['metadata_dirs'],
            isDevMode: $settings['dev_mode'],
            proxyDir: $settings['proxy_dir'],
            cache: $settings['cache_dir'] ? new FilesystemAdapter('', 0, $settings['cache_dir']) : new ArrayAdapter()
        );

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        $connection = DriverManager::getConnection(
            $settings['connection'],
            $config
        );

        return new EntityManager($connection, $config);
    },

    Connection::class => static function (ContainerInterface $container): Connection {
        $em = $container->get(EntityManagerInterface::class);
        return $em->getConnection();
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'connection' => [
                'driver' => env('DB_DRIVER'),
                'host' => env('DB_HOST'),
                'user' => env('DB_USER'),
                'password' => env('DB_PASSWORD'),
                'dbname' => env('DB_NAME'),
                'charset' => env('DB_CHARSET'),
            ],
            'metadata_dirs' => [
                __DIR__ . '/../../src/Modules/Battle/Entity',
                __DIR__ . '/../../src/Modules/Identity/Entity',
                __DIR__ . '/../../src/Modules/OAuth/Entity',
            ],
            'types' => [],
        ],
    ],
];
