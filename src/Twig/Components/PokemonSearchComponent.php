<?php

namespace App\Twig\Components;

use App\Entity\TypePokemon;
use App\Services\PokemonSearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent()]
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

    #[LiveProp(writable: true)]
    public array $types = [];

    #[LiveProp(writable: true)]
    public array $pokemons = [];

    public function __construct(
        private readonly PokemonSearchService $searchService,
        private readonly EntityManagerInterface $em,
    )
    {
        $availableTypes = $this->em->getRepository(TypePokemon::class)->findAll();
        foreach ($availableTypes as $type) {
            $this->types[] = ['id' => $type->getId(), 'name' => $type->getName()];
        }
        $this->updateResults();
    }

    #[LiveAction]
    public function updateResults(): void
    {
        dd($this->query);
        $this->pokemons = $this->searchService->search(
            $this->query,
            $this->selectedTypes,
            $this->minWeight,
            $this->maxWeight
        )['hits'];
    }

}
