<?php

namespace practice\entities;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\Level;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Random;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use practice\PPlayer;

final class EnderPearlEntity extends EnderPearl {

    public const NETWORK_ID = self::ENDER_PEARL;

    /**
     * @var float
     */
    public $height = 0.2;

    /**
     * @var float
     */
    public $width = 0.2;

    /**
     * @var float
     */
    protected $gravity = 0.1;

    /**
     * @param Level $level
     * @param CompoundTag $nbt
     * @param Entity|null $owner
     */
    public function __construct(Level $level, CompoundTag $nbt, ?Entity $owner=null) {
        parent::__construct($level, $nbt, $owner);
        if ($owner instanceof PPlayer) {
            $this->setPosition($owner->getPosition()->asVector3()->add(0, $owner->getEyeHeight()));
            $this->setMotion($owner->getDirectionVector()->multiply(1));
            $this->handleMotion($this->motion->x, $this->motion->y, $this->motion->z, 0.6, 0.4);
        }
    }

    /**
     * @return int
     */
    public function getResultDamage(): int {
        return -1;
    }

    /**
     * @param ProjectileHitEvent $event
     * @return void
     */
    protected function onHit(ProjectileHitEvent $event):void{
        $owner = $this->getOwningEntity();
        if ($owner instanceof PPlayer) {
            $this->getLevel()->broadcastLevelEvent($owner, LevelEventPacket::EVENT_PARTICLE_ENDERMAN_TELEPORT);
            $this->getLevel()->addSound(new EndermanTeleportSound($owner));
            if ($owner->canTeleport()) {
                $owner->teleport($event->getRayTraceResult()->getHitVector());
                $owner->setCanTeleport(false);
            }
            $this->getLevel()->addSound(new EndermanTeleportSound($owner));
        }
    }

    /**
     * @param float $x
     * @param float $y
     * @param float $z
     * @param float $f1
     * @param float $f2
     * @return void
     * @noinspection DuplicatedCode
     */
    public function handleMotion(float $x, float $y, float $z, float $f1, float $f2): void {
        $random = new Random();
        $f = sqrt($x * $x + $y * $y + $z * $z);
        $x = $x / $f;
        $y = $y / $f;
        $z = $z / $f;
        $x = $x + $random->nextSignedFloat() * 0.007499999832361937 * $f2;
        $y = $y + $random->nextSignedFloat() * 0.008599999832361937 * $f2;
        $z = $z + $random->nextSignedFloat() * 0.007499999832361937 * $f2;
        $x = $x * $f1;
        $y = $y * $f1;
        $z = $z * $f1;
        $this->motion->x += $x;
        $this->motion->y += $y * 1.40;
        $this->motion->z += $z;
    }

    /**
     * @param int $tickDiff
     * @return bool
     */
    public function entityBaseTick(int $tickDiff = 1):bool{
        $hasUpdate = parent::entityBaseTick($tickDiff);
        $owner = $this->getOwningEntity();
        if (is_null($owner) || !$owner->isAlive() || $owner->isClosed() || $this->isCollided) {
            $this->flagForDespawn();
        }
        return $hasUpdate;
    }

    /**
     * @return void
     */
    public function applyGravity(): void {
        if ($this->isUnderwater()) {
            $this->motion->y += $this->gravity;
        } else {
            parent::applyGravity();
        }
    }

}
