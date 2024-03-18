<?php

namespace practice\listeners;

use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\Listener;
use pocketmine\level\Level;

final class LevelListeners implements Listener {

    /**
     * @param LevelLoadEvent $event
     * @return void
     */
    public function onLoad(LevelLoadEvent $event): void {
        $level = $event->getLevel();
        $level->setTime(Level::TIME_NOON);
        $level->stopTime();
    }

}
