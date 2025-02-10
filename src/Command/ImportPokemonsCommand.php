<?php

namespace App\Command;

use App\Entity\Pokemon;
use App\Entity\TypePokemon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:importPokemons',
    description: 'Add a short description for your command',
)]
class ImportPokemonsCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $typePokemonRepository = $this->entityManager->getRepository(TypePokemon::class);
        $request = $this->httpClient->request('GET', 'https://tyradex.app/api/v1/pokemon');
        $res = json_decode($request->getContent(), true);
        $progressBar = new ProgressBar($output, count($res));
        $progressBar->start();
        foreach ($res as $i => $pokemonToInsert) {

            $pokemon = new Pokemon();
            $pokemon->setPokedexId($pokemonToInsert['pokedex_id']);
            $pokemon->setName($pokemonToInsert['name']['fr']);
            $pokemon->setImage($pokemonToInsert['sprites']['regular']);
            $pokemon->setHeight($pokemonToInsert['height'] ?? "0");
            $pokemon->setWeight($pokemonToInsert['weight'] ?? "0");
            if(null !== $pokemonToInsert['types']) {
                foreach ($pokemonToInsert['types'] as $type) {
                    if(null != $type['name'])
                    {
                        $type = $typePokemonRepository->findOneBy(['name' => $type['name']]);
                        if(null !== $type) {
                            $pokemon->addType($type);
                        }
                    }
                }
            }

            $this->entityManager->persist($pokemon);
            if($i%20 === 0) {
                $this->entityManager->flush();
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        return Command::SUCCESS;
    }
}
