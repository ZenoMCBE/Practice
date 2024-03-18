<?php

namespace practice\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\permission\Permission;
use pocketmine\Server;
use practice\commands\PracticeCommand;
use practice\PPlayer;
use practice\utils\Utils;

final class WorldsCommand extends PracticeCommand {

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("worlds", "Se téléporter dans un monde", "/worlds <monde>", constraint: self::CONSTRAINT_PLAYER_ONLY);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        if (isset($args[0])) {
            $level = Server::getInstance()->getLevelByName($args[0]);
            if ($level instanceof Level) {
                $sender->teleport($level->getSpawnLocation());
            } else {
                $sender->sendMessage(Utils::PREFIX . "§cLe monde " . $args[0] . " n'existe pas.");
            }
        } else {
            $this->sendUsage($sender);
        }
    }

}
