<?php

namespace App\Command;

use App\Model\TempoService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

#[AsCommand(
    name: 'app:tempo',
    description: 'Tempo command',
)]
class TempoCommand extends Command
{
    private const COLORS_STDOUT  = ["bleu" => "<fg=blue>bleu</>", "blanc" => "<fg=white>blanc</>", "rouge" => "<fg=red>rouge</>"];
    private const COLORS_SLACK = ['bleu' => ':large_blue_square:', 'rouge' => ':large_red_square:', 'blanc' => ':large_white_square:'];

    public function __construct(private readonly TempoService $service, private readonly ChatterInterface $slack) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addOption('bypass-cache', null, InputOption::VALUE_NONE, 'Force use of RTE API bypassing DB result cache');
        $this->addOption('slack', null, InputOption::VALUE_NONE, 'Send to Slack channel instead of stdout');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io    = new SymfonyStyle($input, $output);
        $tempo = $this->service->getTempoColor($input->getOption('bypass-cache'));
        $slack = $input->getOption('slack');

        $format = new \IntlDateFormatter('fr');
        $format->setPattern('EEEE d MMM Y');
        $message = sprintf('%s, le %s %s un jour Tempo %s',
                $tempo->isToday() ? "Aujourd'hui" : "Demain",
                $format->format($tempo->getDay()),
                $tempo->isToday() ? "est" : "sera",
                $slack ? self::COLORS_SLACK[$tempo->getColorName()]: self::COLORS_STDOUT[$tempo->getColorName()]);
        if (!$slack) {
            $io->text($message);
            if ($tempo->isToday()) {
                $io->text("Pour connaitre la couleur de demain, revenez après 11h");
            }
        } else {
            $message = new ChatMessage($message);
            $this->slack->send($message);
        }
        return Command::SUCCESS;
    }
}