<?php

namespace practice\handlers\childs;

use practice\datas\{Data, DefaultData};
use pocketmine\utils\SingletonTrait;
use practice\handlers\{HandlerTrait, IHandler};
use practice\PPlayer;
use practice\utils\ids\Statistic;

final class StatisticsHandler implements IHandler, Data, DefaultData {

    use HandlerTrait, SingletonTrait;

    /**
     * @var array
     */
    private array $cache = [];

    /**
     * @return string
     */
    public function getName(): string {
        return "Statistics";
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
    }

    /**
     * @return array
     */
    public function getCache(): array {
        return $this->cache;
    }

    /**
     * @param PPlayer $player
     * @return bool
     */
    public function exist(PPlayer $player): bool {
        return array_key_exists($player->getUpperName(), $this->getCache());
    }

    /**
     * @param PPlayer $player
     * @param string $statistic
     * @return int
     */
    public function get(PPlayer $player, string $statistic): int {
        return $this->cache[$player->getUpperName()][$statistic] ?? $this->getDefaultData()[$statistic];
    }

    /**
     * @param PPlayer $player
     * @param string $statistic
     * @param int $amount
     * @return void
     */
    public function add(PPlayer $player, string $statistic, int $amount = 1): void {
        if ($this->exist($player)) {
            $this->cache[$player->getUpperName()][$statistic] = $this->get($player, $statistic) + $amount;
        }
    }

    /**
     * @param PPlayer $player
     * @param string $statistic
     * @param int $amount
     * @return void
     */
    public function reduce(PPlayer $player, string $statistic, int $amount = 1): void {
        if ($this->exist($player)) {
            $this->cache[$player->getUpperName()][$statistic] = max($this->get($player, $statistic) - $amount, 0);
        }
    }

    /**
     * @param PPlayer $player
     * @param string $statistic
     * @param int $amount
     * @return void
     */
    public function set(PPlayer $player, string $statistic, int $amount): void {
        if ($this->exist($player)) {
            $this->cache[$player->getUpperName()][$statistic] = $amount;
        }
    }

    /**
     * @param PPlayer $player
     * @return float|null
     */
    public function getKdr(PPlayer $player): ?float {
        [$kill, $death] = [$this->get($player, Statistic::KILL), $this->get($player, Statistic::DEATH)];
        return ($kill > 0 && $death > 0) ? round($kill / $death, 2) : null;
    }

    /**
     * @param string $statistic
     * @return array
     */
    public function getTop(string $statistic): array {
        $leaderboard = [];
        foreach ($this->getCache() as $player => $stats) {
            if (array_key_exists($statistic, $stats)) {
                $leaderboard[$player] = $stats[$statistic];
            }
        }
        arsort($leaderboard);
        return $leaderboard;
    }

    /**
     * @param PPlayer $player
     * @return void
     */
    public function setDefaultData(PPlayer $player): void {
        if (!$this->exist($player)) {
            $this->cache[$player->getUpperName()] = $this->getDefaultData();
        }
    }

    /**
     * @return int[]
     */
    public function getDefaultData(): array {
        return [
            Statistic::KILL => 0,
            Statistic::DEATH => 0
        ];
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
