<?php

namespace practice\listeners;

use pocketmine\event\inventory\{CraftItemEvent,
    FurnaceBurnEvent,
    FurnaceCookEvent,
    FurnaceSmeltEvent,
    InventoryPickupArrowEvent,
    InventoryPickupItemEvent,
    InventoryTransactionEvent};
use pocketmine\event\Listener;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use practice\PPlayer;

final class InventoryListeners implements Listener {

    /**
     * @param CraftItemEvent $event
     * @return void
     */
    public function onCraftItem(CraftItemEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param InventoryPickupArrowEvent $event
     * @return void
     */
    public function onPickupArrow(InventoryPickupArrowEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param InventoryPickupItemEvent $event
     * @return void
     */
    public function onPickupItem(InventoryPickupItemEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param InventoryTransactionEvent $event
     * @return void
     */
    public function onTransaction(InventoryTransactionEvent $event): void {
        $transaction = $event->getTransaction();
        $source = $transaction->getSource();
        if ($source instanceof PPlayer) {
            foreach ($transaction->getInventories() as $inventory) {
                if (!$inventory instanceof ArmorInventory) {
                    foreach ($transaction->getActions() as $action) {
                        if (!$action instanceof SlotChangeAction) {
                            $event->setCancelled();
                        }
                    }
                } else {
                    $event->setCancelled();
                }
            }
        }
    }

    /**
     * @param FurnaceBurnEvent $event
     * @return void
     */
    public function onFurnaceBurn(FurnaceBurnEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param FurnaceCookEvent $event
     * @return void
     */
    public function onFurnaceCook(FurnaceCookEvent $event): void {
        $event->setCancelled();
    }

    /**
     * @param FurnaceSmeltEvent $event
     * @return void
     */
    public function onFurnaceSmelt(FurnaceSmeltEvent $event): void {
        $event->setCancelled();
    }

}
