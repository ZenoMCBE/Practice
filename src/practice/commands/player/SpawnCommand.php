<?php

namespace practice\commands\player;

use pocketmine\command\CommandSender;
use practice\commands\PracticeCommand;
use practice\PPlayer;
use practice\utils\Utils;

final class SpawnCommand extends PracticeCommand {

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("spawn", "Se téléporter au spawn", null, ["hub", "lobby"], self::CONSTRAINT_PLAYER_ONLY);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $sender->teleportToLobby();
        $sender->sendMessage(Utils::PREFIX . "§fVous venez de vous téléporter au lobby !");
    }

}
