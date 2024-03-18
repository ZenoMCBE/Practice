<?php

namespace practice\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use practice\commands\PracticeCommand;
use practice\PPlayer;
use practice\utils\Utils;

final class BuildCommand extends PracticeCommand {

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("build", "Activer/désactiver le mode build", null, [], self::CONSTRAINT_PLAYER_ONLY);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $mode = !$sender->isBuild();
        $sender->setBuild($mode);
        $format = $mode ? "§aActivé" : "§cDésactivé";
        $sender->sendMessage(Utils::PREFIX . "§fVous venez de définir le mode build à " . $format . " §f!");
    }

}
