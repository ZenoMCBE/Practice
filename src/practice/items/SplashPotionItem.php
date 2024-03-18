<?php

namespace practice\items;

use pocketmine\entity\Entity;
use pocketmine\item\SplashPotion;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\Player;

final class SplashPotionItem extends SplashPotion {

    /**
     * @return int
     */
    public function getMaxStackSize(): int {
        return 1;
    }

    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return bool
     */
    public function onClickAir(Player $player, Vector3 $directionVector): bool {
        $motion = $player->getDirectionVector()->multiply(0.5);
        $nbt = Entity::createBaseNBT($player->add(0), $motion);
        $splashPotion = Entity::createEntity($this->getProjectileEntityType(), $player->getLevel(), $nbt, $player);
        $splashPotion->spawnToAll();
        $player->broadcastEntityEvent(AnimatePacket::ACTION_SWING_ARM);
        $this->pop();
        return true;
    }

    /**
     * @return string
     */
    public function getProjectileEntityType(): string {
        return "CustomSplashPotion";
    }

    /**
     * @return float
     */
    public function getThrowForce(): float {
        return 0.5;
    }

}
