<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture
{
    private const TEAMS = [
        'Акрон (Тольятти)',
        'Амкар (Пермь)',
        'Волга (Нижний Новгород)',
        'Волга (Ульяновск)',
        'Газовик-Газпром (Ижевск)',
        'Гастелло (Уфа)',
        'Динамо (Киров)',
        'Дружба (Йошкар-Ола)',
        'Звезда (Пермь)',
        'Зенит (Ижевск)',
        'КАМАЗ (Набережные Челны)',
        'Крылья Советов (Самара)',
        'Лада (Тольятти)',
        'Лада-Симбирск (Димитровград)',
        'Локомотив (Нижний Новгород)',
        'Мордовия (Саранск)',
        'Нефтехимик (Нижнекамск)',
        'Нижний Новгород (осн. 2007)',
        'Нижний Новгород (осн. 2015)',
        'Оренбург (Оренбург)',
        'Рубин (Казань)',
        'Содовик (Стерлитамак)',
        'Сокол (Саратов)',
        'Спартак (Нижний Новгород)',
        'Торпедо (Арзамас)',
        'Торпедо-Виктория (Нижний Новгород)',
        'Уфа',
        'Химик (Дзержинск)',
        'Носта (Новотроицк)',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TEAMS as $teamName) {
            $manager->persist(new Team($teamName));
        }

        $manager->flush();
    }
}
