<?php

namespace practice\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use practice\commands\PracticeCommand;
use practice\forms\FormTrait;
use practice\handlers\HandlerTrait;
use practice\PPlayer;

final class KnockbackCommand extends PracticeCommand {

    use FormTrait, HandlerTrait;

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("knockback", "Gérer les knockback d'une arène FFA", null, ["kb"], self::CONSTRAINT_PLAYER_ONLY);
        $this->setPermission(Permission::DEFAULT_OP);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $form = (isset($args[0]) && $this->getFfaHandler()->isValidFfa($args[0]))
            ? $this->getFfaForms()->getFfaKnockbackInformationsForm($args[0])
            : $this->getFfaForms()->getFfaListForm();
        $sender->sendForm($form);
    }

}
