<?php

namespace practice\loaders\childs;

use practice\datas\NoProviderData;
use practice\handlers\{childs\FfaHandler,
    childs\KitsHandler,
    childs\RanksHandler,
    childs\ScoreboardHandler,
    childs\StatisticsHandler,
    HandlerTrait,
    IHandler};
use practice\loaders\ILoader;
use practice\utils\NonAutomaticLoad;

final class HandlersLoader implements ILoader {

    use HandlerTrait;

    /**
     * @return void
     */
    public function onLoad(): void {
        /* @var IHandler[] $handlers */
        $handlers = [
            FfaHandler::getInstance(),
            KitsHandler::getInstance(),
            RanksHandler::getInstance(),
            ScoreboardHandler::getInstance(),
            StatisticsHandler::getInstance()
        ];
        foreach ($handlers as $handler) {
            if (
                isset(class_implements($handler)[IHandler::class]) &&
                !isset(class_implements($handler)[NonAutomaticLoad::class])
            ) {
                if (!isset(class_implements($handler)[NoProviderData::class])) {
                    $this->getProvidersHandler()->register($handler->getName());
                }
                $handler->onEnable();
            }
        }
    }

    /**
     * @return void
     */
    public function onUnload(): void {
        /* @var IHandler[] $handlers */
        $handlers = [
            FfaHandler::getInstance(),
            KitsHandler::getInstance(),
            RanksHandler::getInstance(),
            ScoreboardHandler::getInstance(),
            StatisticsHandler::getInstance()
        ];
        foreach ($handlers as $handler) {
            if (
                isset(class_implements($handler)[IHandler::class]) &&
                !isset(class_implements($handler)[NonAutomaticLoad::class])
            ) {
                if (!isset(class_implements($handler)[NoProviderData::class])) {
                    $this->getProvidersHandler()->register($handler->getName());
                }
                $handler->onDisable();
            }
        }
    }

}
