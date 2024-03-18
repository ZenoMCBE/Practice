<?php

namespace practice\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Server;
use practice\commands\PracticeCommand;
use practice\handlers\HandlerTrait;
use practice\PPlayer;
use practice\utils\ids\Rank;
use practice\utils\Utils;

final class SetRankCommand extends PracticeCommand {

    use HandlerTrait;

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("setrank", "Définir un grade à un joueur", "/setrank <joueur> <grade>", [], self::CONSTRAINT_PLAYER_ONLY);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        if (isset($args[0], $args[1])) {
            $target = Utils::getPlayer($args[0]);
            if ($target instanceof PPlayer) {
                if ($this->getRanksHandler()->isValidRank($args[1])) {
                    $this->getRanksHandler()->set($target, $args[1]);
                    Server::getInstance()->broadcastMessage(Utils::PREFIX . "§c§k!§r§6§k!§r§e§k!§r§a§k!§r§9§k!§r §a" . $target->getName() . " §fvient de recevoir le grade " . $this->getRanksHandler()->getColorByRank($args[1]) . ucfirst($args[1]) . " §f! §9§k!§r§a§k!§r§e§k!§r§6§k!§r§c§k!");
                    $this->getScoreboardHandler()->updateRank($target);
                    $target->updateNametag();
                } else {
                    $sender->sendMessage(Utils::PREFIX . "§cLe grade " . $args[1] . " n'existe pas. Voici la liste des grades disponibles : " . implode(", " , Rank::ALL) . ".");
                }
            } else {
                $sender->sendMessage(Utils::PREFIX . "§cLe joueur " . $args[0] . " n'existe pas.");
            }
        } else {
            $this->sendUsage($sender);
        }
    }

}
