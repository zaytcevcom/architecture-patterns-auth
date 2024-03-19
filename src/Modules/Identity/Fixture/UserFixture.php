<?php

declare(strict_types=1);

namespace App\Modules\Identity\Fixture;

use App\Modules\Identity\Entity\User\User;
use App\Modules\Identity\Service\PasswordHasher;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $hasher = new PasswordHasher();

        $user = User::signup(
            login: 'Zaytcev',
            passwordHash: $hasher->hash('1234567890'),
        );

        $manager->persist($user);

        $manager->flush();
    }
}
