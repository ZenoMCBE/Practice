<?php

namespace practice\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\permission\Permission;
use pocketmine\Server;
use practice\commands\PracticeCommand;
use practice\handlers\HandlerTrait;
use practice\PPlayer;

final class EntitiesCommand extends PracticeCommand {

    use HandlerTrait;

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("entities", "Faire apparaître les classements", constraint: self::CONSTRAINT_PLAYER_ONLY);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $defaultLevel = Server::getInstance()->getDefaultLevel();
        $entities = [
            ["TopK", new Position(-196.5, 31, 176.5, $defaultLevel)],
            ["TopD", new Position(-188.5, 31, 176.5, $defaultLevel)]
        ];
        foreach ($entities as $entityData) {
            [$name, $position] = $entityData;
            $nbt = Entity::createBaseNBT($position);
            $leaderboard = Entity::createEntity($name, $defaultLevel, $nbt);
            $leaderboard->spawnToAll();
        }
    }

}
