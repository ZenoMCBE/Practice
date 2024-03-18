<?php

namespace practice\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerAchievementAwardedEvent,
    PlayerBucketEmptyEvent,
    PlayerBucketFillEvent,
    PlayerChatEvent,
    PlayerCreationEvent,
    PlayerDeathEvent,
    PlayerDropItemEvent,
    PlayerExhaustEvent,
    PlayerInteractEvent,
    PlayerJoinEvent,
    PlayerQuitEvent,
    PlayerRespawnEvent};
use pocketmine\item\{ItemFactory, ItemIds};
use pocketmine\level\Location;
use pocketmine\permission\Permission;
use pocketmine\Server;
use practice\forms\FormTrait;
use practice\handlers\HandlerTrait;
use practice\PPlayer;
use practice\utils\ids\Cooldown;
use practice\utils\ids\Scoreboard;

final class PlayerListeners implements Listener {

    use FormTrait, HandlerTrait;

    /**
     * @param PlayerCreationEvent $event
     * @return void
     */
    public function onCreation(PlayerCreationEvent $event): void {
        $event->setPlayerClass(PPlayer::class);
    }

    /**
     * @param PlayerJoinEvent $event
     * @return void
     */
    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $event->setJoinMessage("");
        if ($player instanceof PPlayer) {
            $player->updateNametag();
            $player->teleportToLobby();
            $this->initializeData($player);
            $player->sendTitle("§l§q» §r§aZeno Practice §r§q«", "§r§7Bienvenue " . $player->getName() . " sur Zeno Practice !");
            $this->getScoreboardHandler()->sendScoreboard($player, Scoreboard::LOBBY, true);
            Server::getInstance()->broadcastPopup("§a+ " . $player->getName() . " +");
        }
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $event->setQuitMessage("");
        if ($player instanceof PPlayer) {
            if ($player->isInCombat()) {
                $player->onCustomDeath(new PlayerDeathEvent($player, [], null, 0));
            }
            Server::getInstance()->broadcastPopup("§c- " . $player->getName() . " -");
        }
    }

    /**
     * @param PlayerRespawnEvent $event
     * @return void
     */
    public function onRespawn(PlayerRespawnEvent $event): void {
        $player = $event->getPlayer();
        if ($player instanceof PPlayer) {
            $event->setRespawnPosition(new Location(-192.5, 29, 191.5, 180, 0, Server::getInstance()->getLevelByName("lobby")));
            $player->teleportToLobby();
        }
    }

    /**
     * @param PlayerDeathEvent $event
     * @return void
     */
    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        if ($player instanceof PPlayer) {
            $player->onCustomDeath($event);
        }
    }

    /**
     * @param PlayerChatEvent $event
     * @return void
     */
    public function onChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        if ($player instanceof PPlayer) {
            if (!$player->isInCooldown(Cooldown::CHAT)) {
                if (!$player->hasPermission(Permission::DEFAULT_OP)) {
                    $player->addCooldown(Cooldown::CHAT, 2);
                }
                $event->setFormat($this->getRanksHandler()->formatMessage($player, $event->getMessage()));
            } else {
                $event->setCancelled();
            }
        }
    }

    /**
     * @param PlayerExhaustEvent $event
     * @return void
     */
    public function onExhaust(PlayerExhaustEvent $event): void {
        $player = $event->getPlayer();
        $player->setFood(20);
        $player->setSaturation(20);
        $event->setAmount(0);
        $event->setCancelled();
    }

    /**
     * @param PlayerDropItemEvent $event
     * @return void
     */
    public function onDropItem(PlayerDropItemEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if ($player instanceof PPlayer) {
            $item = $event->getItem();
            $action = $event->getAction();
            if ($action === $event::RIGHT_CLICK_BLOCK || $action === $event::RIGHT_CLICK_AIR) {
                switch ($item->getId()) {
                    case ItemIds::COMPASS:
                        if (!$player->isInCooldown(Cooldown::FORM)) {
                            $player->sendForm($this->getFfaForms()->getFfaTeleportForm());
                            $player->addCooldown(Cooldown::FORM, 1);
                        }
                        break;
                    case ItemIds::SLIME_BALL:
                        $event->setCancelled();
                        if ($player->getHealth() < 18) {
                            $player->setHealth(min($player->getHealth() + 4, $player->getMaxHealth()));
                            $player->getInventory()->removeItem(ItemFactory::get(ItemIds::SLIME_BALL));
                        }
                        break;
                }
            } else if ($action === $event::LEFT_CLICK_BLOCK && $item->getId() === ItemIds::SLIME_BALL) {
                $event->setCancelled();
                if ($player->getHealth() < 18) {
                    $player->setHealth(min($player->getHealth() + 4, $player->getMaxHealth()));
                    $player->getInventory()->removeItem(ItemFactory::get(ItemIds::SLIME_BALL));
                }
            }
        }
    }

    /**
     * @param PlayerAchievementAwardedEvent $event
     * @return void
     */
    public function onAchievementAwarded(PlayerAchievementAwardedEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param PlayerBucketEmptyEvent $event
     * @return void
     */
    public function onBucketEmpty(PlayerBucketEmptyEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param PlayerBucketFillEvent $event
     * @return void
     */
    public function onBucketFill(PlayerBucketFillEvent $event): void {
        $event->setCancelled();
    }

}
