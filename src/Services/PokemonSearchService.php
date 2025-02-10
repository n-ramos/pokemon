<?php

namespace App\Services;

use Nramos\SearchIndexer\Indexer\SearchClientInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PokemonSearchService

{
    public function __construct(
        private readonly HttpClientInterface $client
    )
    {
    }

    public function search(string $query, array $selectedTypes, ?int $minWeight, ?int $maxWeight): array
    {
        if($query === "") {
            $query = "*";
        }
        $dataToSend = [
            'q' => $query,
            'limit' => 10,
            'page' => 0
        ];

        $result = $this->client->request('POST', 'http://127.0.0.1:7700/indexes/pokemon/search', [
            'headers' => ["Accept" => "application/json", "Authorization" => "Bearer masterKey"],
            "json" =>
                $dataToSend

        ]);
        $result = json_decode($result->getContent(), true);

        return $result;
    }
}