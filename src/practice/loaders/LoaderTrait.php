<?php

namespace practice\loaders;

use practice\loaders\childs\CommandsLoader;
use practice\loaders\childs\EntitiesLoader;
use practice\loaders\childs\HandlersLoader;
use practice\loaders\childs\ItemsLoader;
use practice\loaders\childs\ListenersLoader;
use practice\loaders\childs\LevelsLoader;
use practice\utils\NonAutomaticLoad;

trait LoaderTrait {

    /**
     * @return void
     */
    public function loadAll(): void {
        $loaders = [
            new CommandsLoader(),
            new EntitiesLoader(),
            new HandlersLoader(),
            new ItemsLoader(),
            new LevelsLoader(),
            new ListenersLoader()
        ];
        foreach ($loaders as $loader) {
            if (
                isset(class_implements($loader)[ILoader::class]) &&
                !isset(class_implements($loader)[NonAutomaticLoad::class])
            ) {
                $loader->onLoad();
            }
        }
    }

    /**
     * @return void
     */
    public function unloadAll(): void {
        $loaders = [
            new CommandsLoader(),
            new EntitiesLoader(),
            new HandlersLoader(),
            new ItemsLoader(),
            new LevelsLoader(),
            new ListenersLoader()
        ];
        foreach ($loaders as $loader) {
            if (
                isset(class_implements($loader)[ILoader::class]) &&
                !isset(class_implements($loader)[NonAutomaticLoad::class])
            ) {
                $loader->onUnload();
            }
        }
    }

}
