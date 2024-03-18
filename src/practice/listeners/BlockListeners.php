<?php

namespace practice\listeners;

use pocketmine\event\block\{BlockBreakEvent,
    BlockBurnEvent,
    BlockFormEvent,
    BlockGrowEvent,
    BlockPlaceEvent,
    BlockSpreadEvent,
    BlockUpdateEvent,
    LeavesDecayEvent,
    SignChangeEvent,
    SignOpenEditEvent,
    SignTextColorChangeEvent};
use pocketmine\event\Listener;
use pocketmine\permission\Permission;
use practice\PPlayer;

final class BlockListeners implements Listener {

    /**
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        if (
            !$player instanceof PPlayer ||
            !$player->hasPermission(Permission::DEFAULT_OP) ||
            !$player->isBuild()
        ) {
            $event->setCancelled();
        }
    }

    /**
     * @param BlockPlaceEvent $event
     * @return void
     */
    public function onPlace(BlockPlaceEvent $event): void {
        $player = $event->getPlayer();
        if (
            !$player instanceof PPlayer ||
            !$player->hasPermission(Permission::DEFAULT_OP) ||
            !$player->isBuild()
        ) {
            $event->setCancelled();
        }
    }

    /**
     * @param BlockBurnEvent $event
     * @return void
     */
    public function onBurn(BlockBurnEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param BlockFormEvent $event
     * @return void
     */
    public function onForm(BlockFormEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param BlockGrowEvent $event
     * @return void
     */
    public function onGrow(BlockGrowEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param BlockSpreadEvent $event
     * @return void
     */
    public function onSpread(BlockSpreadEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param BlockUpdateEvent $event
     * @return void
     */
    public function onUpdate(BlockUpdateEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param LeavesDecayEvent $event
     * @return void
     */
    public function onLeavesDecay(LeavesDecayEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param SignChangeEvent $event
     * @return void
     */
    public function onSignChange(SignChangeEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param SignOpenEditEvent $event
     * @return void
     */
    public function onSignOpenEdit(SignOpenEditEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param SignTextColorChangeEvent $event
     * @return void
     */
    public function onSignTextColorChange(SignTextColorChangeEvent $event): void {
        $event->setCancelled();
    }

}
