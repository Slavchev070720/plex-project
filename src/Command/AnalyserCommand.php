<?php

namespace App\Command;

use App\Service\AnalyseService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class AnalyserCommand extends Command
{
    protected static $defaultName = 'app:analyser';

    /**
     * @var AnalyseService
     */
    protected $analyseService;

    /**
     * @param AnalyseService $analyseService
     */
    public function __construct(AnalyseService $analyseService)
    {
        $this->analyseService = $analyseService;

        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Analyse Start!');
        try{
            $this->analyseService->analyseSQLiteDB();
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
        }
        $output->writeln('Analyse Complete!');
    }
}
