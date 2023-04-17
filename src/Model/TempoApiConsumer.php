<?php

namespace App\Model;

use App\Entity\ApiToken;
use App\Entity\TempoDayColor;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

// https://data.rte-france.com/catalog/-/api/consumption/Tempo-Like-Supply-Contract/v1.1
class TempoApiConsumer
{
    private const API_COLORS    = ["BLUE" => TempoColors::BLEU, "WHITE" => TempoColors::BLANC, "RED" => TempoColors::ROUGE];
    private const API_OAUTH_URI = 'https://digital.iservices.rte-france.com/token/oauth/';
    private const API_TEMPO_URI = 'https://digital.iservices.rte-france.com/open_api/tempo_like_supply_contract/v1/tempo_like_calendars';

    public function __construct(private readonly HttpClientInterface $client, private readonly string $apiLogin, private readonly string $apiPass)
    {

    }

    public function getOAuthToken(): ?ApiToken
    {
        try {
            $httpResponse = $this->client->request("POST", self::API_OAUTH_URI , ['auth_basic' => [$this->apiLogin, $this->apiPass],
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']]);
            $response = \json_decode($httpResponse->getContent());
        } catch (ClientException $e) {
            throw new ApiConsumerException(previous: $e);
        }
        if (!$httpResponse->getStatusCode() == 200) {
            throw new ApiConsumerException(sprintf("API returned code %d", $httpResponse->getStatusCode()));
        }
        $token = new ApiToken();
        $token->setToken($response->access_token);
        $token->setExpiry(new \DateTime(sprintf('now + %d seconds', $response->expires_in)));

        return $token;
    }
    
    public function getTempoColor(ApiToken $token): TempoDayColor
    {
        $response = json_decode($this->client->request('GET', self::API_TEMPO_URI , ['auth_bearer' => (string)$token])->getContent());
        $data     = $response->tempo_like_calendars->values[0];
        if (!self::isValidString($data->value)) {
            throw new ApiConsumerException("Unknown tempo color returned from API");
        }

        $tempoColor = new TempoDayColor();
        $tempoColor->setColor(self::API_COLORS[$data->value]);
        $tempoColor->setDay(new \DateTime($data->start_date));

        return $tempoColor;
    }

    private static function isValidString(string $color): bool
    {
        return array_key_exists($color, self::API_COLORS);
    }
}