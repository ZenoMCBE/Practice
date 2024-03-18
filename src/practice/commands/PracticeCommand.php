<?php

namespace practice\commands;

use pocketmine\command\{Command, CommandSender, ConsoleCommandSender};
use pocketmine\permission\Permission;
use pocketmine\plugin\Plugin;
use practice\PPlayer;
use practice\Practice;
use practice\utils\Utils;

abstract class PracticeCommand extends Command {

    public const CONSTRAINT_PLAYER_ONLY = 0;
    public const CONSTRAINT_CONSOLE_ONLY = 1;

    /**
     * @var int|null
     */
    private ?int $constraint;

    public function __construct(
        string $name,
        string $description = "",
        ?string $usageMessage = null,
        array $aliases = [],
        ?int $constraint = null,
        ?array $overloads = null
    ) {
        parent::__construct($name, $description, $usageMessage, $aliases, $overloads);
        $this->constraint = $constraint;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        switch (true) {
            case !$this->testPermissionSilent($sender):
                $sender->sendMessage(Utils::PREFIX . "§cVous ne disposez pas des permissions nécessaires pour utiliser cette commande");
                break;
            case !$sender instanceof PPlayer && $this->isConstraint(self::CONSTRAINT_PLAYER_ONLY):
                $sender->sendMessage(Utils::PREFIX . "§cVous ne pouvez pas utiliser cette commande depuis la console.");
                break;
            case !$sender instanceof ConsoleCommandSender && $this->isConstraint(self::CONSTRAINT_CONSOLE_ONLY):
                $sender->sendMessage(Utils::PREFIX . "§cVous ne pouvez pas utiliser cette commande en jeu.");
                break;
            default:
                $this->onRun($sender, $args);
                break;
        }
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    abstract public function onRun(CommandSender $sender, array $args): void;

    /**
     * @param CommandSender $sender
     * @return void
     */
    public function sendUsage(CommandSender $sender): void {
        $sender->sendMessage(Utils::PREFIX . "§fVous devez faire §a" . $this->getUsage() . " §fpour §a" . strtolower($this->getDescription()) . " §f!");
    }

    /**
     * @param CommandSender $target
     * @return bool
     */
    public function testPermissionSilent(CommandSender $target): bool {
        return parent::testPermissionSilent($target) || $target->hasPermission(Permission::DEFAULT_OP);
    }

    /**
     * @param int $constraint
     * @return bool
     */
    private function isConstraint(int $constraint): bool {
        return $this->getConstraint() === $constraint;
    }

    /**
     * @return int|null
     */
    public function getConstraint(): ?int {
        return $this->constraint;
    }

}
