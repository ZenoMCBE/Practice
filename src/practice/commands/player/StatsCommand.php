<?php

namespace practice\commands\player;

use pocketmine\command\CommandSender;
use practice\commands\PracticeCommand;
use practice\handlers\HandlerTrait;
use practice\PPlayer;
use practice\utils\ids\Statistic;
use practice\utils\Utils;

final class StatsCommand extends PracticeCommand {

    use HandlerTrait;

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("stats", "Consulter les statistiques d'un joueur", "/stats <joueur>", constraint: self::CONSTRAINT_PLAYER_ONLY);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $showStats = function (PPlayer $sender, PPlayer $target): void {
            $sender->sendMessage("§l§q» §r§aStatistique de " . $target->getName() . " §l§q«");
            $sender->sendMessage("§l§q| §r§fKill(s)§8: §b" . $this->getStatisticsHandler()->get($target, Statistic::KILL));
            $sender->sendMessage("§l§q| §r§fMort(s)§8: §c" . $this->getStatisticsHandler()->get($target, Statistic::DEATH));
            $sender->sendMessage("§l§q| §r§fKD/R(s)§8: §e" . $this->getStatisticsHandler()->getKdr($target));
        };
        if (isset($args[0])) {
            $target = Utils::getPlayer($args[0]);
            if ($target instanceof PPlayer) {
                $showStats($sender, $target);
            } else {
                $sender->sendMessage(Utils::PREFIX . "§cLe joueur " . $args[0] . " n'existe pas.");
            }
        } else {
            $showStats($sender, $sender);
        }
    }

}
