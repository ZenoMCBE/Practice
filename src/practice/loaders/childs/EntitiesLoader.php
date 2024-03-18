<?php

namespace practice\loaders\childs;

use pocketmine\entity\Entity;
use practice\entities\{EnderPearlEntity,
    floatingtext\TopDeathFloatingTextEntity,
    floatingtext\TopKillFloatingTextEntity,
    SplashPotionEntity};
use practice\loaders\ILoader;
use practice\Practice;

final class EntitiesLoader implements ILoader {

    /**
     * @return void
     */
    public function onLoad(): void {
        Entity::registerEntity(TopDeathFloatingTextEntity::class, true, ["TopD"]);
        Entity::registerEntity(TopKillFloatingTextEntity::class, true, ["TopK"]);
        Entity::registerEntity(SplashPotionEntity::class, true, ['CustomSplashPotion', 'minecraft:potion']);
        Entity::registerEntity(EnderPearlEntity::class, true, ['CustomEnderPearl', 'minecraft:ender_pearl']);
        /*var_dump(Entity::registerEntity(TopDeathFloatingTextEntity::class, true));
        var_dump(Entity::registerEntity(TopKillFloatingTextEntity::class, true));
        var_dump(Entity::registerEntity(SplashPotionEntity::class, true, ['CustomSplashPotion', 'minecraft:potion']));
        var_dump(Entity::registerEntity(EnderPearlEntity::class, true, ['CustomEnderPearl', 'minecraft:ender_pearl']));*/
        Practice::getInstance()->getLogger()->notice("[Entity Toutes les entités ont été enregistrée(s) !");
    }

    /**
     * @return void
     */
    public function onUnload(): void {}

}
