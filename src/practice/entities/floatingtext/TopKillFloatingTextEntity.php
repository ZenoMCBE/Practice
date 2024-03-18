<?php

namespace practice\entities\floatingtext;

use pocketmine\entity\{EntityIds, Monster};
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent};
use pocketmine\item\ItemIds;
use pocketmine\permission\Permission;
use pocketmine\utils\TextFormat;
use practice\handlers\HandlerTrait;
use practice\PPlayer;
use practice\utils\ids\Statistic;
use practice\utils\Utils;

final class TopKillFloatingTextEntity extends Monster {

    use HandlerTrait;

    const NETWORK_ID = EntityIds::CHICKEN;

    /**
     * @var int $height
     */
    public $height = 0.7;

    /**
     * @var int $width
     */
    public $width = 0.4;

    /**
     * @var int
     */
    public $gravity = 0;

    /**
     * @return string
     */
    public function getName(): string {
        return "TopKill";
    }

    /**
     * @return void
     */
    public function initEntity(): void {
        parent::initEntity();
        $this->setImmobile();
        $this->setHealth($this->getMaxHealth());
        $this->setNameTagAlwaysVisible();
        $this->setScale(0.001);
        $this->setAffectedByGravity(false);
    }

    /**
     * @param EntityDamageEvent $source
     * @return void
     */
    public function attack(EntityDamageEvent $source): void {
        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();
            if ($damager instanceof PPlayer) {
                if (
                    $damager->getInventory()->getItemInHand()->getId() == ItemIds::RECORD_WAIT &&
                    $damager->hasPermission(Permission::DEFAULT_OP)
                ) {
                    $source->getEntity()->kill();
                }
            }
        }
        $source->setCancelled();
    }

    /**
     * @param int $currentTick
     * @return bool
     * @noinspection DuplicatedCode
     */
    public function onUpdate(int $currentTick): bool {
        $position = 1;
        $leaderboard = array_slice($this->getStatisticsHandler()->getTop(Statistic::KILL), 0, 10);
        $content = "§l§qTOP KILL";
        foreach ($leaderboard as $player => $stats) {
            $content .= TextFormat::EOL . "§a" . $position . ". " . $this->getRanksHandler()->getColorByRank($this->getRanksHandler()->get($player)) . Utils::getPlayerName($player) . " §8(§7" . $stats . "§8)";
            $position++;
        }
        $this->setNameTag($content);
        return parent::onUpdate($currentTick);
    }

}
