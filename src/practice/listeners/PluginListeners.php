<?php

namespace practice\listeners;

use pocketmine\event\Listener;
use pocketmine\event\plugin\{PluginDisableEvent, PluginEnableEvent};
use pocketmine\Server;
use practice\loaders\LoaderTrait;
use practice\Practice;

final class PluginListeners implements Listener {

    use LoaderTrait;

    /**
     * @param PluginEnableEvent $event
     * @return void
     */
    public function onEnable(PluginEnableEvent $event): void {
        $plugin = $event->getPlugin();
        if ($plugin->getName() == Practice::getInstance()->getName()) {
            $plugin->getServer()->getNetwork()->setName("§qPractice");
        }
    }

    /**
     * @param PluginDisableEvent $event
     * @return void
     */
    public function onDisable(PluginDisableEvent $event): void {
        if ($event->getPlugin()->getName() == Practice::getInstance()->getName()) {
            $this->unloadAll();
            foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                $onlinePlayer->transfer("zenoranked.eu");
            }
        }
    }

}
