<?php

namespace App\Twig\Components;

use App\Services\PokemonSearchService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('pokemon_list')]
final class PokemonListComponent
{
    use DefaultActionTrait;
    #[LiveProp]
    public array $pokemons = [];

    private PokemonSearchService $searchService;

    public function __construct(PokemonSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function updateResults(string $query, array $selectedTypes, ?int $minWeight, ?int $maxWeight): void
    {
        $this->pokemons = $this->searchService->search($query, $selectedTypes, $minWeight, $maxWeight);
    }
}
