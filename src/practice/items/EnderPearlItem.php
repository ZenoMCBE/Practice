<?php

namespace practice\items;

use pocketmine\item\EnderPearl;

final class EnderPearlItem extends EnderPearl {

    /**
     * @return int
     */
    public function getMaxStackSize(): int {
        return 16;
    }

    /**
     * @return string
     */
    public function getProjectileEntityType(): string {
        return "CustomEnderPearl";
    }

    /**
     * @return float
     */
    public function getThrowForce(): float {
        return 1.8;
    }

    /**
     * @return int
     */
    public function getCooldownTicks(): int {
        return 20;
    }

}
