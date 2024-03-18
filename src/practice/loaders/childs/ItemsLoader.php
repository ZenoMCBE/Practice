<?php

namespace practice\loaders\childs;

use pocketmine\item\ItemFactory;
use practice\items\{EnderPearlItem, GoldenAppleItem, SplashPotionItem};
use practice\loaders\ILoader;
use practice\Practice;

final class ItemsLoader implements ILoader {

    /**
     * @return void
     */
    public function onLoad(): void {
        $items = [
            new EnderPearlItem(),
            new GoldenAppleItem(),
            new SplashPotionItem()
        ];
        foreach ($items as $item) {
            ItemFactory::registerItem($item, true);
        }
        Practice::getInstance()->getLogger()->notice("[Item] " . count($items) . " item(s) enregistrée(s) !");
    }

    /**
     * @return void
     */
    public function onUnload(): void {}

}
