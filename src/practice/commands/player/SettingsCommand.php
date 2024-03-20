<?php

namespace practice\commands\player;

use pocketmine\command\CommandSender;
use practice\commands\PracticeCommand;
use practice\forms\FormTrait;
use practice\PPlayer;

final class SettingsCommand extends PracticeCommand {

    use FormTrait;

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("settings", "Gérer ses paramètres", constraint: self::CONSTRAINT_PLAYER_ONLY);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $sender->sendForm($this->getSettingsForms()->getForm($sender));
    }

}
