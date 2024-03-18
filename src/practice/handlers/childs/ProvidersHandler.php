<?php

namespace practice\handlers\childs;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use practice\handlers\IHandler;
use practice\Practice;

final class ProvidersHandler implements IHandler {

    use SingletonTrait;

    /**
     * @var array
     */
    private array $providers = [];

    /**
     * @return string
     */
    public function getName(): string {
        return "Provider";
    }

    /**
     * @return void
     */
    public function onEnable(): void {}

    /**
     * @param string $provider
     * @return void
     */
    public function register(string $provider): void {
        if (!array_key_exists($provider, $this->providers)) {
            $this->providers[$provider] = new Config(Practice::getInstance()->getDataFolder() . $provider . ".json", Config::JSON);
        }
    }

    /**
     * @param string $provider
     * @return Config|null
     */
    public function getProvider(string $provider): ?Config {
        return $this->providers[$provider] ?? null;
    }

    /**
     * @return void
     */
    public function onDisable(): void {}

}
