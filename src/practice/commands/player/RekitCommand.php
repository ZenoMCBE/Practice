<?php

namespace practice\commands\player;

use pocketmine\command\CommandSender;
use practice\commands\PracticeCommand;
use practice\handlers\HandlerTrait;
use practice\PPlayer;
use practice\utils\Utils;

final class RekitCommand extends PracticeCommand {

    use HandlerTrait;

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("rekit", "Se redonner le kit d'une arène", constraint: self::CONSTRAINT_PLAYER_ONLY);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $ffa = $this->getFfaHandler()->getFfaByLevel($sender->getLevel());
        if (!is_null($ffa)) {
            $kit = $this->getKitsHandler()->getKitByFfa($ffa);
            if (!is_null($kit)) {
                $this->getKitsHandler()->send($sender, $kit);
                $sender->sendMessage(Utils::PREFIX . "§fVous venez de vous rekit dans l'arène §a" . $this->getFfaHandler()->getFfaName($ffa) . " §f!");
            } else {
                $sender->sendMessage(Utils::PREFIX . "§cAucun kit n'est relié à votre arène.");
            }
        } else {
            $sender->sendMessage(Utils::PREFIX . "§cVous ne vous trouvez pas dans une arène FFA.");
        }
    }

}
