<?php

namespace practice\handlers\childs;

use pocketmine\utils\SingletonTrait;
use practice\datas\{Data, DefaultData};
use pocketmine\utils\TextFormat;
use practice\handlers\{HandlerTrait, IHandler};
use practice\PPlayer;
use practice\utils\ids\Rank;
use practice\utils\Utils;

final class RanksHandler implements IHandler, Data, DefaultData {

    use HandlerTrait, SingletonTrait;

    /**
     * @var array
     */
    private array $cache = [];

    /**
     * @return string
     */
    public function getName(): string {
        return "Ranks";
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
     * @param PPlayer|string $player
     * @return string
     */
    public function get(PPlayer|string $player): string {
        $playerName = Utils::getPlayerName($player);
        return $this->cache[$playerName] ?? $this->getDefaultData();
    }

    /**
     * @param PPlayer $player
     * @param string $rank
     * @return void
     */
    public function set(PPlayer $player, string $rank): void {
        if ($this->exist($player)) {
            $this->cache[$player->getUpperName()] = $rank;
        }
    }

    /**
     * @param PPlayer $player
     * @param string $message
     * @return string
     */
    public function formatMessage(PPlayer $player, string $message): string {
        $rank = $this->get($player);
        $color = $this->getColorByRank($rank);
        $format = $rank == Rank::PLAYER
            ? $color . "{NAME} §l§8» §r§f{MSG}"
            : $color . "{NAME} §8[" . $color . "{RANK}§8] §l§8» §r" . $color . "{MSG}";
        return str_replace(["{NAME}", "{RANK}", "{MSG}"], [$player->getName(), ucfirst($rank), TextFormat::clean($message)], $format);
    }

    /**
     * @param string $rank
     * @return string
     */
    public function getColorByRank(string $rank): string {
        return match ($rank) {
            Rank::PLAYER => TextFormat::GREEN,
            Rank::STAR => TextFormat::BLUE,
            Rank::MEDIA => "§u",
            Rank::STAFF => TextFormat::YELLOW,
            Rank::ADMIN => TextFormat::GOLD,
            Rank::OWNER => TextFormat::DARK_RED
        };
    }

    /**
     * @param string $rank
     * @return bool
     */
    public function isValidRank(string $rank): bool {
        return in_array($rank, Rank::ALL);
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
     * @return string
     */
    public function getDefaultData(): string {
        return Rank::PLAYER;
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
