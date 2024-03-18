<?php

namespace practice\commands\player;

use pocketmine\command\CommandSender;
use practice\commands\PracticeCommand;
use practice\PPlayer;
use practice\utils\Utils;

final class PingCommand extends PracticeCommand {

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("ping", "Connaître la latence d'un joueur", null, [], self::CONSTRAINT_PLAYER_ONLY);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $formatPing = fn (int $ping): string => $ping > 201 ? "§c$ping ms" : ($ping >= 101 && $ping <= 200 ? "§6$ping ms" : "§a$ping ms");
        if (isset($args[0])) {
            $target = Utils::getPlayer($args[0]);
            if ($target instanceof PPlayer) {
                $targetPing = $target->getPing();
                $sender->sendMessage(Utils::PREFIX . "§fLe joueur §a" . $target->getName() . " §fpossède " . $formatPing($targetPing) . " §f!");
            } else {
                $sender->sendMessage(Utils::PREFIX . "§cLe joueur " . $args[0] . " n'existe pas.");
            }
        } else {
            $selfPing = $sender->getPing();
            $sender->sendMessage(Utils::PREFIX . "§fVous possédez " . $formatPing($selfPing) . " §f!");
        }
    }

}
