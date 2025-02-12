<?php

namespace App\Services;

use Nramos\SearchIndexer\Indexer\SearchClientInterface;
use Nramos\SearchIndexer\Meilisearch\Filter\MeiliSearchFilter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PokemonSearchService

{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly SearchClientInterface $searchClient
    )
    {
    }

    public function search(string $query, array $selectedTypes, ?int $minWeight, ?int $maxWeight): \Nramos\SearchIndexer\Dto\SearchResultCollectionDto
    {
        $searchFilters = new MeiliSearchFilter();
        if (!empty($selectedTypes)) {
            // Utilisation de filtres stricts "AND"
            foreach ($selectedTypes as $type) {
                $searchFilters->addFilter("types.name", "=", $type);
            }
        }

        return $this->searchClient->search('pokemon', $query, filters: $searchFilters, facets: ['types']);
    }
}