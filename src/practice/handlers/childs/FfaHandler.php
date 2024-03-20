<?php

namespace practice\handlers\childs;

use pocketmine\level\{Level, Position};
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use practice\datas\Data;
use practice\handlers\{HandlerTrait, IHandler};
use practice\PPlayer;
use practice\utils\ids\FFA;
use practice\utils\Utils;

final class FfaHandler implements IHandler, Data {

    use HandlerTrait, SingletonTrait;

    /**
     * @var array
     */
    private array $cache = [];

    /**
     * @return string
     */
    public function getName(): string {
        return "FFA";
    }

    /**
     * @return void
     */
    public function onEnable(): void {
        $this->loadCache();
    }

    /**
     * @return void
     */
    public function loadCache(): void {
        $this->cache = $this->getProvider($this)?->getAll();
        foreach ($this->getFFAs() as $ffa) {
            if (!array_key_exists($ffa, $this->getCache())) {
                $this->cache[$ffa] = $this->getDefaultKnockback();
            }
        }
    }

    /**
     * @return array
     */
    public function getCache(): array {
        return $this->cache;
    }

    /**
     * @param PPlayer $player
     * @param string $ffa
     * @param bool $respawn
     * @return void
     */
    public function teleport(PPlayer $player, string $ffa, bool $respawn = false): void {
        $player->teleport($this->findRandomPosition($ffa));
        if (!$respawn) {
            $player->sendMessage("§l§4» §r§cDISCLAIMER §l§4«");
            $player->sendMessage("§l§4| §r§7Les knockbacks ne sont pas définitifs, ils sont susceptibles de changer n'importe quand !");
            $player->sendMessage("§l§4| §r§7N'hésitez pas à donner vos suggestions à §cMyma§7, §cDarkly§7, §cMesdame §7ou d'autres staffs !");
        }
        $level = $this->getLevelByFfa($ffa);
        foreach ($level->getPlayers() as $levelPlayer) {
            assert($levelPlayer instanceof PPlayer);
            $levelPlayer->updateHidingNonOpponents();
        }
    }

    /**
     * @param string $ffa
     * @return Position
     */
    private function findRandomPosition(string $ffa): Position {
        $positionData = $this->getFfaPosition($ffa);
        [$x, $y, $z] = [$positionData["x"], $positionData["y"], $positionData["z"]];
        [$minX, $maxX, $minZ, $maxZ] = array_merge($x, $z);
        return new Position(mt_rand($minX, $maxX), $y, mt_rand($minZ, $maxZ), $this->getLevelByFfa($ffa));
    }

    /**
     * @param string $ffa
     * @return int
     */
    public function countFfaPlayers(string $ffa): int {
        return count($this->getLevelByFfa($ffa)->getPlayers());
    }

    /**
     * @param string $ffa
     * @return array
     */
    public function getKnockback(string $ffa): array {
        return $this->cache[$ffa] ?? $this->getDefaultKnockback();
    }

    /**
     * @param string $ffa
     * @param int|float $xz
     * @param int|float $y
     * @param int $hitDelay
     * @param int|float $heightLimit
     * @return void
     */
    public function setKnockback(string $ffa, int|float $xz = 0.4, int|float $y = 0.4, int $hitDelay = 10, int|float $heightLimit = 4): void {
        $this->cache[$ffa] = [$xz, $y, $hitDelay, $heightLimit];
    }

    /**
     * @return array
     */
    public function getDefaultKnockback(): array {
        return [0.4, 0.4, 10, 4];
    }

    /**
     * @param Level $level
     * @return string|null
     */
    public function getFfaByLevel(Level $level): ?string {
        return FFA::FFA[$level->getFolderName()] ?? null;
    }

    /**
     * @param string $ffa
     * @return Level
     */
    public function getLevelByFfa(string $ffa): Level {
        $level = Server::getInstance()->getLevelByName(FFA::LEVEL[$ffa]);
        assert($level instanceof Level);
        return $level;
    }

    /**
     * @param string $ffa
     * @return string
     */
    public function getFfaName(string $ffa): string {
        return FFA::NAME[$ffa];
    }

    /**
     * @param string $ffa
     * @return string
     */
    public function getFfaImage(string $ffa): string {
        return FFA::IMAGE[$ffa];
    }

    /**
     * @param string $ffa
     * @return array
     */
    public function getFfaPosition(string $ffa): array {
        return FFA::POSITION[$ffa];
    }

    /**
     * @param string $ffa
     * @return bool
     */
    public function isValidFfa(string $ffa): bool {
        return in_array($ffa, $this->getFFAs());
    }

    /**
     * @return array
     */
    public function getFFAs(): array {
        return FFA::ALL;
    }

    /**
     * @return void
     */
    public function unloadCache(): void {
        $provider = $this->getProvider($this);
        $provider?->setAll($this->getCache());
        $provider?->save();
    }

    /**
     * @return void
     */
    public function onDisable(): void {
        $this->unloadCache();
    }

}
