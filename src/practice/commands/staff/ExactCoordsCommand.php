<?php

namespace practice\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use practice\commands\PracticeCommand;
use practice\PPlayer;
use practice\utils\Utils;

final class ExactCoordsCommand extends PracticeCommand {

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("exactcoords", "Obtenir sa position exacte", constraint: self::CONSTRAINT_PLAYER_ONLY);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $location = $sender->getLocation();
        $indexes = [
            "X" => $location->getX(),
            "Y" => $location->getY(),
            "Z" => $location->getZ(),
            "YAW" => $location->getYaw(),
            "PITCH" => $location->getPitch(),
        ];
        $coords = array_map(fn (string $key, float $value): string => "$key: " . round($value, 2), array_keys($indexes), $indexes);
        $sender->sendMessage(Utils::PREFIX . "§fVoici votre position exacte : §a" . implode(" | ", $coords) . " §f!");
    }

}
