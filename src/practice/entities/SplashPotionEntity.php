<?php

namespace practice\entities;

use pocketmine\entity\{EffectInstance, Entity};
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\entity\{EntityRegainHealthEvent, ProjectileHitEntityEvent, ProjectileHitEvent};
use pocketmine\item\Potion as ItemPotion;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\{LevelEventPacket, LevelSoundEventPacket};
use pocketmine\utils\{Color, Random};
use practice\PPlayer;

final class SplashPotionEntity extends SplashPotion {

    public const NETWORK_ID = self::SPLASH_POTION;

    /**
     * @var float
     */
    public $height = 0.1;

    /**
     * @var float
     */
    public $width = 0.1;

    /**
     * @var float
     */
    protected $gravity = 0.05;

    /**
     * @var float
     */
    protected $drag = 0.01;

    /**
     * @param Level $level
     * @param CompoundTag $nbt
     * @param Entity|null $owner
     */
    public function __construct(Level $level, CompoundTag $nbt, ?Entity $owner = null) {
        parent::__construct($level, $nbt, $owner);
        if (!is_null($owner)) {
            $this->setPosition($this->add(0, $owner->getEyeHeight()));
            $this->handleMotion($this->motion->x, $this->motion->y, $this->motion->z, -0.15, 0);
        }
    }

    /**
     * @return void
     */
    protected function initEntity(): void {
        parent::initEntity();
        $this->setPotionId($this->namedtag->getShort("PotionId", 22));
    }

    /**
     * @return void
     */
    public function saveNBT(): void {
        parent::saveNBT();
        $this->namedtag->setShort("PotionId", $this->getPotionId());
    }

    /**
     * @return int
     */
    public function getResultDamage(): int {
        return -1;
    }

    /**
     * @return int
     */
    public function getPotionId(): int {
        return $this->propertyManager->getShort(self::DATA_POTION_AUX_VALUE) ?? 22;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setPotionId(int $id): void {
        $this->propertyManager->setShort(self::DATA_POTION_AUX_VALUE, $id);
    }

    /**
     * @param ProjectileHitEvent $event
     * @return void
     */
    protected function onHit(ProjectileHitEvent $event): void {
        $effects = $this->getPotionEffects();
        [$colors, $hasEffects] = count($effects) === 0
            ? [[new Color(0x38, 0x5D, 0xC6)], false]
            : [[new Color(0xF8, 0x24, 0x23)], true];
        $this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_PARTICLE_SPLASH, Color::mix(...$colors)->toARGB());
        $this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_GLASS);
        if ($hasEffects) {
            foreach ($this->getLevel()->getNearbyEntities($this->getBoundingBox()->expand(1.7, 5.7, 1.7)) as $nearby) {
                if ($nearby instanceof PPlayer && $nearby->isAlive()) {
                    $multiplier = min(1 - (sqrt($nearby->distanceSquared($this)) / 6.15), 0.578);
                    if ($event instanceof ProjectileHitEntityEvent && $nearby->getId() === $event->getEntityHit()->getId()) {
                        $multiplier = 0.580;
                    }
                    foreach ($this->getPotionEffects() as $effect) {
                        $nearby->heal(new EntityRegainHealthEvent($nearby, (4 << $effect->getAmplifier()) * $multiplier * 1.75, EntityRegainHealthEvent::CAUSE_CUSTOM));
                    }
                }
            }
        }
    }

    /**
     * @return array|EffectInstance[]
     */
    public function getPotionEffects(): array {
        return ItemPotion::getPotionEffectsById($this->getPotionId());
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
        $this->motion->y += $y;
        $this->motion->z += $z;
    }

    /**
     * @param int $tickDiff
     * @return bool
     */
    public function entityBaseTick(int $tickDiff = 1): bool {
        $hasUpdate = parent::entityBaseTick($tickDiff);
        if ($this->isCollided) {
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
