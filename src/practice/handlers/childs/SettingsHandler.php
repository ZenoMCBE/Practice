<?php

namespace practice\handlers\childs;

use practice\datas\{Data, DefaultData};
use pocketmine\utils\SingletonTrait;
use practice\handlers\HandlerTrait;
use practice\handlers\IHandler;
use practice\PPlayer;
use practice\utils\ids\Setting;

final class SettingsHandler implements IHandler, Data, DefaultData {

    use HandlerTrait, SingletonTrait;

    /**
     * @var array
     */
    private array $cache = [];

    /**
     * @return string
     */
    public function getName(): string {
        return "Settings";
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
        $this->cache = $this->getProvider($this)->getAll();
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
     * @param string $setting
     * @return bool
     */
    public function has(PPlayer $player, string $setting): bool {
        return $this->get($player, $setting);
    }

    /**
     * @param PPlayer $player
     * @param string $setting
     * @return bool
     */
    public function get(PPlayer $player, string $setting): bool {
        return $this->cache[$player->getUpperName()][$setting] ?? $this->getDefaultData()[$setting];
    }

    /**
     * @param PPlayer $player
     * @param string $setting
     * @param bool $value
     * @return void
     */
    public function set(PPlayer $player, string $setting, bool $value): void {
        if ($this->exist($player)) {
            $this->cache[$player->getUpperName()][$setting] = $value;
        }
    }

    /**
     * @param string $setting
     * @return string
     */
    public function getSettingName(string $setting): string {
        return Setting::NAME[$setting] ?? "";
    }

    /**
     * @return array
     */
    public function getSettings(): array {
        return Setting::ALL;
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
     * @return array
     */
    public function getDefaultData(): array {
        return [
            Setting::HIDE_NON_OPPONENT => true,
            Setting::IMMEDIATE_RESPAWN => true,
            Setting::LIGHTNING_KILL => true,
            Setting::NIGHT_VISION => false,
            Setting::SCOREBOARD => true
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
