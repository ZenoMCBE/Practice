<?php

namespace practice;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use practice\loaders\LoaderTrait;

final class Practice extends PluginBase {

    /**
     * TODO:
     * - Ajouter des permissions aux commandes par défaut de Altay
     */

    use LoaderTrait, SingletonTrait;

    /**
     * @return void
     */
    public function onLoad(): void {
        $this::setInstance($this);
    }

    /**
     * @return void
     */
    public function onEnable(): void {
        $this->loadAll();
        $this->getLogger()->notice("Practice activé.");
    }

    /**
     * @return void
     */
    public function onDisable(): void {
        $this->getLogger()->notice("Practice désactivé.");
    }

}
