<?php

namespace practice\tasks;

use pocketmine\scheduler\Task;
use practice\PPlayer;
use practice\utils\Utils;

final class EnderPearlTask extends Task {

    private const DEFAULT_TIME = 300; // 12 secondes

    /**
     * @var int
     */
    private int $time = self::DEFAULT_TIME;

    /**
     * @param PPlayer $player
     */
    public function __construct(protected PPlayer $player) {}

    /**
     * @param int $currentTick
     * @return void
     */
    public function onRun(int $currentTick): void {
        if ($this->player->isConnected()) {
            if (!$this->player->isInLobby()) {
                $this->player->setXpLevel(max(ceil($this->time / 20), 0));
                $this->player->setXpProgress(Utils::calculateNormalizedValue($this->time, 0, self::DEFAULT_TIME));
                $this->time--;
                if ($this->time <= 0) {
                    if ($this->player->isAlive()) {
                        $this->player->setXpLevel(0);
                        $this->player->setXpProgress(0.0);
                        $this->player->sendTip("§r§l§q» §r§aVous pouvez utiliser votre Ender Pearl §l§q«");
                        $this->player->setCanTeleport(true);
                        $this->getHandler()?->cancel();
                    }
                }
            } else {
                $this->getHandler()?->cancel();
            }
        } else {
            $this->getHandler()?->cancel();
        }
    }

    /**
     * @return void
     */
    public function onCancel(): void {
        if ($this->player->isConnected()) {
            $this->player->setXpLevel(0);
            $this->player->setXpProgress(0.0);
            $this->player->setEnderPearlTask(null);
            $this->player->setCanTeleport(true);
        }
    }

}
