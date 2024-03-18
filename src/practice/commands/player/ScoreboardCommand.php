<?php

namespace practice\commands\player;

use pocketmine\command\CommandSender;
use practice\commands\PracticeCommand;
use practice\handlers\HandlerTrait;
use practice\PPlayer;
use practice\utils\Utils;

final class ScoreboardCommand extends PracticeCommand {

    use HandlerTrait;

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("scoreboard", "Activer/désactiver le scoreboard", constraint: self::CONSTRAINT_PLAYER_ONLY);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $mode = !$this->getScoreboardHandler()->has($sender);
        $this->getScoreboardHandler()->set($sender, $mode);
        $message = $mode
            ? "§aVous venez d'activer le scoreboard !"
            : "§cVous venez de désactiver le scoreboard !";
        $sender->sendMessage(Utils::PREFIX . $message);
    }

}