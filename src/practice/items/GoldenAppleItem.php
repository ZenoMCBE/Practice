<?php

namespace practice\items;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\GoldenApple;

final class GoldenAppleItem extends GoldenApple {

    /**
     * @return bool
     */
    public function requiresHunger(): bool {
        return false;
    }

    /**
     * @return int
     */
    public function getFoodRestore(): int {
        return -1;
    }

    /**
     * @return float
     */
    public function getSaturationRestore(): float {
        return -0.5;
    }

    /**
     * @return EffectInstance[]
     */
    public function getAdditionalEffects(): array {
        return [
            new EffectInstance(Effect::getEffect(Effect::SPEED), 20*60*3, 0, false),
            new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20*5, 1, false),
            new EffectInstance(Effect::getEffect(Effect::ABSORPTION), 20*60*3, 0, false)
        ];
    }

}
