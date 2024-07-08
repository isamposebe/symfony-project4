<?php

namespace App\Service;



class calculationService
{
    /**
     * @param array $a
     * @param array $b
     * @return array[]
     */
    public function matrixTour(array $listTeam): array
    {

        $countlistTeam = count($listTeam);
        $matrixTour = [$countlistTeam][$countlistTeam];
        for ($i = 0; $i < $countlistTeam; $i++) {
            $matrixTour[i][0] = $listTeam[$i];
        }
        $flowers = [ [ "Название" => "фиалки",
            "Стоимость" => 100,
            "Количество" => 15
        ],
            [ "Название" => "астры",
                "Стоимость" => 60,
                "Количество" => 25,
            ],
            [ "Название" => "каллы",
                "Стоимость" => 180,
                "Количество" => 7
            ]
        ];
        return $flowers;
    }
}