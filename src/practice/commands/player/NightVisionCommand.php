<?php

namespace practice\commands\player;

use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use practice\commands\PracticeCommand;
use practice\PPlayer;
use practice\utils\Utils;

final class NightVisionCommand extends PracticeCommand {

    /**
     * CONSTRUCT
     */
    public function __construct() {
        parent::__construct("nightvision", "Activer/désactiver la vision nocturne", null, ["nv"], self::CONSTRAINT_PLAYER_ONLY);
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function onRun(CommandSender $sender, array $args): void {
        assert($sender instanceof PPlayer);
        $mode = !$sender->hasEffect(Effect::NIGHT_VISION);
        if ($mode) {
            $sender->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 60*60*60*60, 1, false));
            $sender->sendMessage(Utils::PREFIX . "§aVous venez d'activer la vision nocturne !");
        } else {
            $sender->removeEffect(Effect::NIGHT_VISION);
            $sender->sendMessage(Utils::PREFIX . "§cVous venez de désactiver la vision nocturne !");
        }
    }

}
