<?php

namespace App\Command;

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
    name: 'app:import-types-pokemons',
    description: 'Add a short description for your command',
)]
class ImportTypesPokemonsCommand extends Command
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
        $getTypesFromApi = $this->httpClient->request("GET", "https://tyradex.app/api/v1/types");
        $res = json_decode($getTypesFromApi->getContent(), true);
        $progressBar = new ProgressBar($output, count($res));
        foreach ($res as $type) {
            $typeToImport = new TypePokemon();
            $typeToImport->setName($type['name']['fr']);
            $typeToImport->setExternalId($type['id']);
            $typeToImport->setImage($type['sprites']);
            $progressBar->advance();
            $this->entityManager->persist($typeToImport);
        }
        $this->entityManager->flush();
        $progressBar->finish();
        return Command::SUCCESS;
    }
}
