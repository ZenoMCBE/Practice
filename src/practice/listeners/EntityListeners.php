<?php

namespace practice\listeners;

use pocketmine\event\entity\{EntityArmorChangeEvent,
    EntityCombustByBlockEvent,
    EntityCombustByEntityEvent,
    EntityCombustEvent,
    EntityDamageByBlockEvent,
    EntityDamageByEntityEvent,
    EntityDamageEvent,
    EntityExplodeEvent,
    EntityTeleportEvent,
    ProjectileHitBlockEvent,
    ProjectileHitEntityEvent,
    ProjectileLaunchEvent};
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\Durable;
use pocketmine\math\Vector3;
use practice\handlers\HandlerTrait;
use practice\{entities\EnderPearlEntity, entities\SplashPotionEntity, PPlayer, Practice};
use practice\tasks\EnderPearlTask;
use practice\utils\ids\Cooldown;
use practice\utils\Utils;

final class EntityListeners implements Listener {

    use HandlerTrait;

    /**
     * @param EntityArmorChangeEvent $event
     * @return void
     */
    public function onArmorChange(EntityArmorChangeEvent $event): void {
        $newItem = $event->getNewItem();
        if ($newItem instanceof Durable && !$newItem->isUnbreakable()) {
            $item = clone $newItem;
            $item->setUnbreakable();
            $event->setNewItem($item);
        }
    }

    /**
     * @param EntityDamageEvent $event
     * @return void
     */
    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if ($entity instanceof PPlayer) {
            $cause = $event->getCause();
            if ($cause === $event::CAUSE_VOID) {
                $lastDamageCause = $entity->getLastDamageCause();
                if ($lastDamageCause instanceof EntityDamageByEntityEvent) {
                    $damager = $lastDamageCause->getDamager();
                    if ($damager instanceof PPlayer) {
                        $entity->onCustomDeath(new PlayerDeathEvent($entity, [], null, 0));
                    }
                } else if ($entity->isInLobby()) {
                    $entity->teleportToLobby();
                }
                $event->setCancelled();
            } else if (in_array($cause, [$event::CAUSE_VOID, $event::CAUSE_FALL, $event::CAUSE_DROWNING, $event::CAUSE_FIRE, $event::CAUSE_FIRE_TICK, $event::CAUSE_LAVA])) {
                $event->setCancelled();
            }
        }
    }

    /**
     * @param EntityDamageByBlockEvent $event
     * @return void
     */
    public function onDamageByBlock(EntityDamageByBlockEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param EntityDamageByEntityEvent $event
     * @return void
     * @noinspection PhpUnusedLocalVariableInspection
     */
    public function onDamageByEntity(EntityDamageByEntityEvent $event): void {
        $entity = $event->getEntity();
        $damager = $event->getDamager();
        if (
            ($entity instanceof PPlayer && $damager instanceof PPlayer) &&
            ($entity->isConnected() && $damager->isConnected()) &&
            ($entity->isAlive() && $damager->isAlive())
        ) {
            if (!$entity->isInLobby() && !$damager->isInLobby()) {
                if ($event->isApplicable($event::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN)) {
                    $event->setCancelled();
                }
                [$entityOpponent, $damagerOpponent] = [$entity->getOpponent(), $damager->getOpponent()];
                if ($entity->isInCombat() && $entityOpponent?->getId() !== $damager->getId()) {
                    if ($entity->hasOpponent()) {
                        $damager->sendMessage(Utils::PREFIX . "§cVous ne pouvez pas attaquer " . $entity->getName() . " car il est déjà en combat contre " . $entityOpponent?->getName() . ".");
                    }
                    $event->setCancelled();
                } else if ($damager->isInCombat() && $damagerOpponent?->getId() !== $entity->getId()) {
                    if ($damager->hasOpponent()) {
                        $damager->sendMessage(Utils::PREFIX . "§cVous ne pouvez pas attaquer " . $entity->getName() . " car vous êtes déjà en combat contre " . $damagerOpponent?->getName() . ".");
                    }
                    $event->setCancelled();
                }
                if (!$event->isCancelled()) {
                    $ffa = $this->getFfaHandler()->getFfaByLevel($damager->getLevel());
                    [$xz, $y, $hitDelay, $heightLimit] = $this->getFfaHandler()->getKnockback($ffa);
                    $event->setKnockBack($xz);
                    $event->setAttackCooldown($hitDelay);
                    $entity->setCombatTime($damager, 30, false);
                    $damager->setCombatTime($entity, 30, false);
                }
            } else {
                $event->setCancelled();
            }
        }
    }

    /**
     * @param EntityCombustByBlockEvent $event
     * @return void
     */
    public function onCombustByBlock(EntityCombustByBlockEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param EntityCombustByEntityEvent $event
     * @return void
     */
    public function onCombustByEntity(EntityCombustByEntityEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param EntityCombustEvent $event
     * @return void
     */
    public function onCombust(EntityCombustEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param EntityExplodeEvent $event
     * @return void
     */
    public function onExplode(EntityExplodeEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param EntityTeleportEvent $event
     * @return void
     */
    public function onTeleport(EntityTeleportEvent $event): void {
        $entity = $event->getEntity();
        if ($entity instanceof PPlayer) {
            [$levelFrom, $levelTo] = [$event->getFrom()->getLevel(), $event->getTo()->getLevel()];
            if ($levelFrom->getFolderName() !== $levelTo->getFolderName()) {
                $scoreboard = $this->getScoreboardHandler()->getScoreboardByLevel($levelTo);
                if (!is_null($scoreboard) && $entity->getScoreboard() !== $scoreboard) {
                    $this->getScoreboardHandler()->sendScoreboard($entity, $scoreboard, true);
                }
                if (!is_null(($levelKit = $this->getKitsHandler()->getKitByLevel($levelTo)))) {
                    $this->getKitsHandler()->send($entity, $levelKit);
                }
            }
        }
    }

    /**
     * @param ProjectileLaunchEvent $event
     * @return void
     */
    public function onProjectileLaunch(ProjectileLaunchEvent $event): void {
        $projectile = $event->getEntity();
        if ($projectile instanceof EnderPearlEntity) {
            $owningEntity = $projectile->getOwningEntity();
            if ($owningEntity instanceof PPlayer) {
                if ($owningEntity->isInCooldown(Cooldown::ENDER_PEARL)) {
                    $event->setCancelled();
                }
                if (!$event->isCancelled()) {
                    $owningEntity->setCanTeleport(true);
                    $owningEntity->addCooldown(Cooldown::ENDER_PEARL, 15);
                    $owningEntity->getEnderPearlTask()?->cancel();
                    $owningEntity->setEnderPearlTask(Practice::getInstance()->getScheduler()->scheduleRepeatingTask(new EnderPearlTask($owningEntity), 1));
                } else {
                    $owningEntity->sendTip(Utils::PREFIX . "§r§l§4» §r§cVous êtes en cooldown d'EnderPearl de " . $owningEntity->getCooldown(Cooldown::ENDER_PEARL, true) . " seconde(s). §l§4«");
                }
            }
        }
    }

    /**
     * @param ProjectileHitBlockEvent $event
     * @return void
     */
    public function onProjectileHitBlock(ProjectileHitBlockEvent $event): void {
        $projectile = $event->getEntity();
        if ($projectile instanceof SplashPotionEntity) {
            $owningEntity = $projectile->getOwningEntity();
            if ($owningEntity instanceof PPlayer) {
                $blockHitLevel = $event->getBlockHit()->getLevel();
                if (
                    $blockHitLevel->getFolderName() == "nodebuff-nitro-ffa" &&
                    $owningEntity->getLevel()->getFolderName() == "nodebuff-nitro-ffa" &&
                    $owningEntity->isAlive()
                ) {
                    $projectile->teleport($owningEntity->getLocation());
                }
            }
        }
    }

    /**
     * @param ProjectileHitEntityEvent $event
     * @return void
     */
    public function onProjectileHitEntity(ProjectileHitEntityEvent $event): void {
        $projectile = $event->getEntity();
        if ($projectile instanceof EnderPearlEntity) {
            $owningEntity = $projectile->getOwningEntity();
            $entityHit = $event->getEntityHit();
            if ($owningEntity instanceof PPlayer && $entityHit instanceof PPlayer) {
                $hitVector = $event->getRayTraceResult()->getHitVector();
                if ($owningEntity->getOpponent()?->getId() == $entityHit->getId()) {
                    [$entityLocation, $projectileLocation] = [$entityHit->getLocation(), $projectile->getLocation()];
                    $deltaX = $entityLocation->getX() - $projectileLocation->getX();
                    $deltaZ = $entityLocation->getZ() - $projectileLocation->getZ();
                    $entityHit->setMotion((new Vector3($deltaX, 0.28, $deltaZ))->subtract($deltaX / 4, 0, $deltaZ / 4));
                }
                $owningEntity->teleport($hitVector);
            }
        }
    }

}
