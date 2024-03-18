<?php

namespace practice\loaders\childs;

use pocketmine\Server;
use practice\loaders\ILoader;
use practice\Practice;

final class LevelsLoader implements ILoader {

    /**
     * @return void
     */
    public function onLoad(): void {
        $server = Server::getInstance();
        foreach (array_diff(scandir($server->getDataPath() . "worlds"), ["..", "."]) as $worldName) {
            $server->loadLevel($worldName);

        }
        Practice::getInstance()->getLogger()->notice("[World] Tous les mondes ont été chargés !");
    }

    /**
     * @return void
     */
    public function onUnload(): void {}

}
