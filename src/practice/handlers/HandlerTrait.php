<?php

namespace practice\handlers;

use pocketmine\utils\Config;
use practice\handlers\childs\FfaHandler;
use practice\handlers\childs\KitsHandler;
use practice\handlers\childs\ProvidersHandler;
use practice\handlers\childs\RanksHandler;
use practice\handlers\childs\ScoreboardHandler;
use practice\handlers\childs\SettingsHandler;
use practice\handlers\childs\StatisticsHandler;
use practice\PPlayer;

trait HandlerTrait {

    /**
     * @return FfaHandler
     */
    public function getFfaHandler(): FfaHandler {
        return FfaHandler::getInstance();
    }

    /**
     * @return KitsHandler
     */
    public function getKitsHandler(): KitsHandler {
        return KitsHandler::getInstance();
    }

    /**
     * @return ProvidersHandler
     */
    public function getProvidersHandler(): ProvidersHandler {
        return ProvidersHandler::getInstance();
    }

    /**
     * @return RanksHandler
     */
    public function getRanksHandler(): RanksHandler {
        return RanksHandler::getInstance();
    }

    /**
     * @return ScoreboardHandler
     */
    public function getScoreboardHandler(): ScoreboardHandler {
        return ScoreboardHandler::getInstance();
    }

    /**
     * @return SettingsHandler
     */
    public function getSettingsHandler(): SettingsHandler {
        return SettingsHandler::getInstance();
    }

    /**
     * @return StatisticsHandler
     */
    public function getStatisticsHandler(): StatisticsHandler {
        return StatisticsHandler::getInstance();
    }

    /**
     * @param PPlayer $player
     * @return void
     */
    final public function initializeData(PPlayer $player): void {
        $this->getRanksHandler()->setDefaultData($player);
        $this->getSettingsHandler()->setDefaultData($player);
        $this->getStatisticsHandler()->setDefaultData($player);
    }

    /**
     * @param IHandler $handler
     * @return Config|null
     */
    final protected function getProvider(IHandler $handler): ?Config {
        return $this->getProvidersHandler()->getProvider($handler->getName());
    }

}
