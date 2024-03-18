<?php

namespace practice\loaders\childs;

use pocketmine\event\Listener;
use pocketmine\Server;
use practice\listeners\{BlockListeners,
    EntityListeners,
    InventoryListeners,
    LevelListeners,
    PlayerListeners,
    PluginListeners,
    ServerListeners};
use practice\loaders\ILoader;
use practice\Practice;
use practice\utils\NonAutomaticLoad;

final class ListenersLoader implements ILoader {

    /**
     * @return void
     */
    public function onLoad(): void {
        $core = Practice::getInstance();
        $listeners = [
            new BlockListeners(),
            new EntityListeners(),
            new InventoryListeners(),
            new LevelListeners(),
            new PlayerListeners(),
            new PluginListeners(),
            new ServerListeners()
        ];
        foreach ($listeners as $listener) {
            if (
                isset(class_implements($listener)[Listener::class]) &&
                !isset(class_implements($listener)[NonAutomaticLoad::class])
            ) {
                Server::getInstance()->getPluginManager()->registerEvents($listener, $core);
            }
        }
        $core->getLogger()->notice("[Listener] " . count($listeners) . " listener(s) enregistré(s) !");
    }

    /**
     * @return void
     */
    public function onUnload(): void {}

}
