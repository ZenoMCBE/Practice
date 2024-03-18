<?php

namespace practice\listeners;

use pocketmine\event\Listener;
use pocketmine\event\server\{CommandEvent, DataPacketSendEvent, QueryRegenerateEvent};
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\{DisconnectPacket,
    LevelSoundEventPacket,
    SetTimePacket,
    StartGamePacket,
    TextPacket};
use pocketmine\permission\Permission;
use pocketmine\Server;
use practice\PPlayer;
use practice\utils\Utils;

final class ServerListeners implements Listener {

    /**
     * @param CommandEvent $event
     * @return void
     */
    public function onCommand(CommandEvent $event): void {
        $sender = $event->getSender();
        if ($sender instanceof PPlayer) {
            if (
                $sender->isInCombat() &&
                !$sender->hasPermission(Permission::DEFAULT_OP) &&
                in_array($event->getCommand(), ["spawn", "hub", "lobby", "rekit"])
            ) {
                $sender->sendMessage(Utils::PREFIX . "§cVous ne pouvez pas utiliser cette commande en combat.");
                $event->setCancelled();
            }
        }
    }

    /**
     * @param DataPacketSendEvent $event
     * @return void
     */
    public function onDataPacketSend(DataPacketSendEvent $event): void {
        $player = $event->getPlayer();
        if ($player instanceof PPlayer) {
            $packet = $event->getPacket();
            switch ($packet) {
                case $packet instanceof DisconnectPacket:
                    $packet->message = str_replace("Kicked by admin. Reason:", "", $packet->message); // ...
                    break;
                case $packet instanceof LevelSoundEventPacket:
                    if (
                        $packet->sound === $packet::SOUND_ATTACK_NODAMAGE ||
                        $packet->sound === $packet::SOUND_ATTACK_STRONG
                    ) {
                        $event->setCancelled();
                    }
                    break;
                case $packet instanceof SetTimePacket:
                    $packet->time = Level::TIME_NOON;
                    break;
                case $packet instanceof StartGamePacket:
                    $packet->emoteChatMuted = true;
                    break;
                case $packet instanceof TextPacket:
                    if ($packet->message == "Internal server error" && !$player->hasPermission(Permission::DEFAULT_OP)) {
                        $event->setCancelled();
                    }
                    break;
            }
        }
    }

    /**
     * @param QueryRegenerateEvent $event
     * @return void
     */
    public function onQueryRegenerate(QueryRegenerateEvent $event): void {
        $event->setMaxPlayerCount(count(Server::getInstance()->getLoggedInPlayers()) + 1);
    }

}
