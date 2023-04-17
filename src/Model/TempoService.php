<?php

namespace App\Model;

use App\Entity\ApiToken;
use App\Entity\TempoDayColor;
use App\Repository\ApiTokenRepository;
use App\Repository\TempoDayColorRepository;

class TempoService
{
    public function __construct(private readonly TempoApiConsumer $api, private readonly ApiTokenRepository $apiTokenRepo, private readonly TempoDayColorRepository $tempoDayColorRepo)
    {
    }

    public function getTempoColor($byPassDatabaseCache = false): TempoDayColor
    {
        $tempoColor = $byPassDatabaseCache ? null : $this->tempoDayColorRepo->findCachedTempoDayColor();

        if (!$tempoColor) {
            $token = $this->apiTokenRepo->findCachedToken();
            if (!$token) {
                $token = $this->api->getOAuthToken();
                $this->saveOAuthToken($token);
            }
            $tempoColor = $this->api->getTempoColor($token);
            $this->tempoDayColorRepo->save($tempoColor, true);
        }

        return $tempoColor;
    }

    private function saveOAuthToken(ApiToken $token): void
    {
        $this->apiTokenRepo->emptyTable();
        $this->apiTokenRepo->save($token, true);
    }
}