<?php

namespace App\Twig\Components;

use AllowDynamicProperties;
use App\Entity\TypePokemon;
use App\Services\PokemonSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AllowDynamicProperties] #[AsLiveComponent()]
final class PokemonSearchComponent
{
    use DefaultActionTrait;
    #[LiveProp(writable: true)]
    public string $query = '';

    #[LiveProp(writable: true)]
    public array $selectedTypes = [];

    #[LiveProp(writable: true)]
    public ?int $minWeight = null;

    #[LiveProp(writable: true)]
    public ?int $maxWeight = null;


    public function __construct(
        private readonly PokemonSearchService $searchService,
        private readonly EntityManagerInterface $em,
    )
    {
        $allTypes = $this->em->getRepository(TypePokemon::class)->findAll();
        foreach ($allTypes as $type) {
             $this->allTypes[$type->getName()] = 0;
        }
    }
    public function types() {

        $pokemonsDto = $this->searchService->search(
            $this->query,
            $this->selectedTypes,
            $this->minWeight,
            $this->maxWeight
        );
        $typesToReturn = $this->allTypes;

        foreach($pokemonsDto->getMeta()->getFacetsDistribution()['types.name'] as $name => $count) {
           $typesToReturn[$name] = $count;
        }
        return $typesToReturn;
    }
    public function pokemons(): array
    {
        return $this->searchService->search(
            $this->query,
            $this->selectedTypes,
            $this->minWeight,
            $this->maxWeight
        )->toArray();
    }

}
