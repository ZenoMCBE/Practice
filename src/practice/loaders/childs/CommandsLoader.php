<?php

namespace practice\loaders\childs;

use pocketmine\command\Command;
use practice\commands\player\{NightVisionCommand,
    PingCommand,
    RekitCommand,
    ScoreboardCommand,
    SpawnCommand,
    StatsCommand,
    TpsCommand};
use practice\commands\staff\{BuildCommand,
    EntitiesCommand,
    ExactCoordsCommand,
    KnockbackCommand,
    SetRankCommand,
    WorldsCommand};
use practice\loaders\ILoader;
use practice\Practice;

final class CommandsLoader implements ILoader {

    /**
     * @var array
     */
    private array $commandsToUnregister = [
        "help", "about", "checkperm", "ppinfo", "kill" , "suicide", "me"
    ];

    /**
     * @return void
     */
    public function onLoad(): void {
        $commands = [
            new BuildCommand(),
            new EntitiesCommand(),
            new ExactCoordsCommand(),
            new KnockbackCommand(),
            new NightVisionCommand(),
            new PingCommand(),
            new RekitCommand(),
            new ScoreboardCommand(),
            new SetRankCommand(),
            new SpawnCommand(),
            new StatsCommand(),
            new TpsCommand(),
            new WorldsCommand()
        ];
        foreach ($this->commandsToUnregister as $commandToUnregister) {
            $defaultCommand = Practice::getInstance()->getServer()->getCommandMap()->getCommand($commandToUnregister);
            if ($defaultCommand instanceof Command) {
                Practice::getInstance()->getServer()->getCommandMap()->unregister($defaultCommand);
            }
        }
        Practice::getInstance()->getLogger()->notice("[Command] " . count($this->commandsToUnregister) . " commande(s) par défaut supprimée(s) !");
        foreach ($commands as $command) {
            Practice::getInstance()->getServer()->getCommandMap()->register($command->getName(), $command);
        }
        Practice::getInstance()->getLogger()->notice("[Command] " . count($commands) . " commande(s) enregistrée(s) !");
    }

    /**
     * @return void
     */
    public function onUnload(): void {}

}
