<?php

namespace practice\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\Server;
use practice\commands\PracticeCommand;

final class TpsCommand extends PracticeCommand {

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("tps", "Voir les performances du serveur");
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        $server = Server::getInstance();
        $format = fn (int $value, array $thresholds, bool $inverse): string => match (true) {
            (!$inverse && $value <= $thresholds[0]) || ($inverse && $value >= $thresholds[0]) => "§c$value",
            (!$inverse && $value <= $thresholds[1]) || ($inverse && $value >= $thresholds[1]) => "§6$value",
            default => "§a$value",
        };
        $tps = $format($server->getTicksPerSecond(), [12, 17], false) . " tick(s)";
        $tu = $format($server->getTickUsage(), [50, 80], true) . "%";
        $tpsAverage = $format($server->getTicksPerSecondAverage(), [12, 17], false) . " tick(s)";
        $tuAverage = $format($server->getTickUsageAverage(), [50, 80], true) . "%";
        $sender->sendMessage("§l§q» §r§aPerformance du serveur §l§q«§r");
        $sender->sendMessage("§l§q| §r§aTPS §8- $tps");
        $sender->sendMessage("§l§q| §r§aTU §8- $tu");
        $sender->sendMessage("§l§q| §r§aTPS/A §8- $tpsAverage");
        $sender->sendMessage("§l§q| §r§aTU/A §8- $tuAverage");
    }

}
