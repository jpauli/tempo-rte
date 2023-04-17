<?php

namespace App\Command;

use App\Model\TempoService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:tempo',
    description: 'Tempo command',
)]
class TempoCommand extends Command
{
    private const COLORS = ["bleu" => "<fg=blue>bleu</>", "blanc" => "<fg=white>blanc</>", "rouge" => "<fg=red>rouge</>"];

    public function __construct(private readonly TempoService $service) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addOption('bypass-cache', null, InputOption::VALUE_NONE, 'Force use of RTE API bypassing DB result cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io    = new SymfonyStyle($input, $output);
        $tempo = $this->service->getTempoColor($input->getOption('bypass-cache'));

        $format = new \IntlDateFormatter('fr');
        $format->setPattern('EEEE d MMM Y');
        $io->text(sprintf('%s, le %s %s un jour Tempo %s', $tempo->isToday() ? "Aujourd'hui" : "Demain", $format->format($tempo->getDay()), $tempo->isToday() ? "est" : "sera", self::COLORS[$tempo->getColorName()]));
        if ($tempo->isToday()) {
            $io->text ("Pour connaitre la couleur de demain, revenez après 11h");
        }
        return Command::SUCCESS;
    }
}