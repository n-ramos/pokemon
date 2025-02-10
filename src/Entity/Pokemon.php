<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nramos\SearchIndexer\Annotation\SearchIndex;
use Nramos\SearchIndexer\Annotation\SearchProperty;
use Nramos\SearchIndexer\Indexer\IndexableEntityInterface;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
#[SearchIndex('pokemon')]
class Pokemon implements IndexableEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[SearchProperty('id', isPk: true, searchable: false)]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $pokedexId = null;

    #[ORM\Column(length: 255)]
    #[SearchProperty('name', filterable: true, sortable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[SearchProperty('image', filterable: false, sortable: false)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[SearchProperty('height', filterable: false, sortable: true)]
    private ?string $height = null;

    #[ORM\Column(length: 255)]
    #[SearchProperty('weight', filterable: false, sortable: true)]
    private ?string $weight = null;

    /**
     * @var Collection<int, TypePokemon>
     */
    #[ORM\ManyToMany(targetEntity: TypePokemon::class, inversedBy: 'pokemon')]
    #[SearchProperty('types', relationProperties: ['name'], sortable: true, filterable: true)]
    private Collection $types;

    public function __construct()
    {
        $this->types = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPokedexId(): ?int
    {
        return $this->pokedexId;
    }

    public function setPokedexId(int $pokedexId): static
    {
        $this->pokedexId = $pokedexId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return Collection<int, TypePokemon>
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(TypePokemon $type): static
    {
        if (!$this->types->contains($type)) {
            $this->types->add($type);
        }

        return $this;
    }

    public function removeType(TypePokemon $type): static
    {
        $this->types->removeElement($type);

        return $this;
    }
}
