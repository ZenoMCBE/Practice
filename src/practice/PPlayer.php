<?php

namespace practice;

use pocketmine\level\Position;
use pocketmine\{entity\Attribute,
    entity\Effect,
    entity\Entity,
    event\entity\EntityDamageByEntityEvent,
    event\player\PlayerDeathEvent,
    item\Item,
    item\ItemFactory,
    item\ItemIds,
    network\mcpe\protocol\types\GameMode,
    permission\Permission,
    Player,
    scheduler\TaskHandler,
    Server};
use practice\handlers\HandlerTrait;
use practice\utils\ids\{FFA, Kit, Scoreboard, Statistic};
use practice\utils\Utils;

final class PPlayer extends Player {

    use HandlerTrait;

    public const STATUS_IDLING = 0;
    public const STATUS_IN_FIGHT = 1;

    /**
     * @var int
     */
    private int $tick = 0;

    /**
     * @var string
     */
    private string $scoreboard = Scoreboard::LOBBY;

    /**
     * @var int
     */
    private int $status = self::STATUS_IDLING;

    /**
     * @var int|null
     */
    private ?int $combatTime = null;

    /**
     * @var PPlayer|null
     */
    private ?self $opponent = null;

    /**
     * @var bool
     */
    private bool $build = false;

    /**
     * @var bool
     */
    private bool $canTeleport = true;

    /**
     * @var array
     */
    private array $cooldowns = [];

    /**
     * @var TaskHandler|null
     */
    private ?TaskHandler $enderPearlTask = null;

    /**
     * @param int $tickDiff
     * @return bool
     */
    public function entityBaseTick(int $tickDiff = 1): bool {
        $tick = parent::entityBaseTick($tickDiff);
        $this->tick++;
        if ($this->tick % 20 === 0) {
            $this->setFood(20);
            if ($this->getGamemode() === GameMode::CREATIVE && !$this->hasPermission(Permission::DEFAULT_OP)) {
                $this->setGamemode(GameMode::SURVIVAL);
            }
            if ($this->isInCombat()) {
                if (time() >= $this->getCombatTime()) {
                    $this->setCombatTime(null, null, true);
                }
            }
            $this->getScoreboardHandler()->updateCombatTime($this);
            if ($this->hasOpponent() && !$this->getOpponent()?->isConnected()) {
                $this->getOpponent()?->setOpponent(null);
                $this->setOpponent(null);
            }
        }
        if ($this->tick % 900 === 0) {
            $this->getScoreboardHandler()->updateOnlinePlayers($this);
            $this->tick = 0;
        }
        return $tick;
    }

    /**
     * @param PlayerDeathEvent $event
     * @return void
     */
    public function onCustomDeath(PlayerDeathEvent $event): void {
        $event->setDrops([]);
        $event->setXpDropAmount(0);
        Utils::doLightning($this);
        $this->getStatisticsHandler()->add($this, Statistic::DEATH);
        $lastDamageCause = $this->getLastDamageCause();
        if ($lastDamageCause instanceof EntityDamageByEntityEvent) {
            $damager = $lastDamageCause->getDamager();
            if ($damager instanceof PPlayer) {
                $damagerLevel = $damager->getLevel();
                $this->getStatisticsHandler()->add($damager, Statistic::KILL);
                $deathMessage = match ($this->getFfaHandler()->getFfaByLevel($damagerLevel)) {
                    FFA::NODEBUFF_ONE, FFA::NODEBUFF_TWO => "§a" . $this->getName() . "§2 [§a" . $this->countItem(ItemFactory::get(ItemIds::SPLASH_POTION, 22)) . " POT(S)§2] §7a été tué par §c" . $damager->getName() . " §4[§c" . $damager->countItem(ItemFactory::get(ItemIds::SPLASH_POTION, 22)) . " POT(S)§4] §7!",
                    FFA::SOUP => "§a" . $this->getName() . "§2 [§a" . $this->countItem(ItemFactory::get(ItemIds::SLIME_BALL)) . " SOUP(S)§2] §7a été tué par §c" . $damager->getName() . " §4[§c" . $damager->countItem(ItemFactory::get(ItemIds::SLIME_BALL)) . " SOUP(S)§4] §7!",
                    default => "§a" . $this->getName() . " §7a été tué par §c" . $damager->getName() . " §7!"
                };
                $event->setDeathMessage(Utils::PREFIX . $deathMessage);
                $this->getKitsHandler()->send($damager, $this->getKitsHandler()->getKitByLevel($damagerLevel));
                if ($this->hasEnderPearlTask()) {
                    $this->getEnderPearlTask()?->cancel();
                }
                $this->setCombatTime(null, null, false, true);
                $damager->setCombatTime(null, null, false);
                $this->getScoreboardHandler()->updateStatistics($damager);
                foreach ([$this, $damager] as $player) {
                    $player->getEnderPearlTask()?->cancel();
                    $player->setEnderPearlTask(null);
                }
            } else {
                $event->setDeathMessage(Utils::PREFIX . "§a" . $this->getName() . " §7est mort !");
            }
        }
    }

    /**
     * @param bool $inverse
     * @param bool $lowerCase
     * @return string
     */
    public function getUpperName(bool $inverse = false, bool $lowerCase = false): string {
        $name = $this->getName();
        $formattedName = $inverse ? str_replace('_', ' ', $name) : str_replace(' ', '_', $name);
        return $lowerCase ? strtolower($formattedName) : $formattedName;
    }

    /**
     * @return void
     */
    public function updateNametag(): void {
        $this->setNameTag($this->getRanksHandler()->getColorByRank($this->getRanksHandler()->get($this)) . $this->getName());
    }

    /**
     * @return bool
     */
    public function isInLobby(): bool {
        return $this->getLevel()->getFolderName() == Server::getInstance()->getDefaultLevel()->getFolderName();
    }

    /**
     * @return void
     */
    public function teleportToLobby(): void {
        $this->teleport(new Position(-192.5, 29, 191.5, Server::getInstance()->getLevelByName("lobby")), 180, 0);
        $this->getKitsHandler()->send($this, Kit::LOBBY);
    }

    /**
     * @return string
     */
    public function getScoreboard(): string {
        return $this->scoreboard;
    }

    /**
     * @param string $scoreboard
     * @return void
     */
    public function setScoreboard(string $scoreboard): void {
        $this->scoreboard = $scoreboard;
    }

    /**
     * @return int
     */
    public function getStatus(): int {
        return $this->status;
    }

    /**
     * @param int $status
     * @return void
     */
    public function setStatus(int $status): void {
        $this->status = $status;
        $this->getScoreboardHandler()->updateStatus($this);
    }

    /**
     * @return string
     */
    public function getFormattedStatus(): string {
        return match ($this->getStatus()) {
            self::STATUS_IDLING => "§aInactif",
            self::STATUS_IN_FIGHT => "§cEn combat",
            default => "§eInconnu"
        };
    }

    /**
     * @return bool
     */
    public function isInCombat(): bool {
        return !is_null($this->getCombatTime());
    }

    /**
     * @param bool $substract
     * @return int|null
     */
    public function getCombatTime(bool $substract = false): ?int {
        return $substract ? $this->combatTime - time() : $this->combatTime;
    }

    /**
     * @param PPlayer|null $opponent
     * @param int|null $combatTime
     * @param bool $automatic
     * @param bool $death
     * @return void
     */
    public function setCombatTime(?PPlayer $opponent, ?int $combatTime, bool $automatic, bool $death = false): void {
        if (!is_null($combatTime)) {
            if (!$this->isInCombat()) {
                $this->sendMessage(Utils::PREFIX . "§cVous êtes en combat ! Ne déconnectez pas sous peine de mourrir instantanément !");
            }
            $this->combatTime = time() + $combatTime;
        } else {
            if ($automatic && !$death) {
                $this->sendMessage(Utils::PREFIX . "§aVous n'êtes plus en combat !");
            }
            $this->combatTime = $combatTime;
        }
        $this->setOpponent($opponent);
        $status = !is_null($combatTime) ? self::STATUS_IN_FIGHT : self::STATUS_IDLING;
        $this->setStatus($status);
    }

    /**
     * @return bool
     */
    public function hasOpponent(): bool {
        return !is_null($this->getOpponent());
    }

    /**
     * @return PPlayer|null
     */
    public function getOpponent(): ?PPlayer {
        return $this->opponent;
    }

    /**
     * @param PPlayer|null $opponent
     * @return void
     */
    public function setOpponent(?PPlayer $opponent): void {
        $this->opponent = $opponent;
    }

    /**
     * @param Item $itemToCount
     * @return int
     */
    public function countItem(Item $itemToCount): int {
        return array_reduce($this->getInventory()->getContents(), function (int $count, Item $item) use ($itemToCount): int {
            return $count + ($item->getId() === $itemToCount->getId() ? $item->getCount() : 0);
        }, 0);
    }

    /**
     * @return bool
     */
    public function isBuild(): bool {
        return $this->build;
    }

    /**
     * @param bool $build
     * @return void
     */
    public function setBuild(bool $build): void {
        $this->build = $build;
    }

    /**
     * @param string $cooldown
     * @return bool
     */
    public function isInCooldown(string $cooldown): bool {
        return array_key_exists($cooldown, $this->cooldowns) && $this->getCooldown($cooldown) > time();
    }

    /**
     * @param string $cooldown
     * @param bool $substract
     * @return int|null
     */
    public function getCooldown(string $cooldown, bool $substract = false): ?int {
        $cooldown = intval($this->cooldowns[$cooldown]) ?? null;
        return !is_null($cooldown) ? ($substract ? $cooldown - time() : $cooldown) : null;
    }

    /**
     * @param string $cooldown
     * @param int $time
     * @return void
     */
    public function addCooldown(string $cooldown, int $time): void {
        $this->cooldowns[$cooldown] = time() + $time;
    }

    /**
     * @return bool
     */
    public function canTeleport(): bool {
        return $this->canTeleport;
    }

    /**
     * @param bool $canTeleport
     * @return void
     */
    public function setCanTeleport(bool $canTeleport): void {
        $this->canTeleport = $canTeleport;
    }

    /**
     * @return bool
     */
    public function hasEnderPearlTask(): bool {
        return !is_null($this->getEnderPearlTask());
    }

    /**
     * @return TaskHandler|null
     */
    public function getEnderPearlTask(): ?TaskHandler {
        return $this->enderPearlTask;
    }

    /**
     * @param TaskHandler|null $enderPearlTask
     * @return void
     */
    public function setEnderPearlTask(?TaskHandler $enderPearlTask): void {
        $this->enderPearlTask = $enderPearlTask;
    }

    /**
     * @param Entity $attacker
     * @param float $damage
     * @param float $x
     * @param float $z
     * @param float $base
     * @return void
     * @noinspection ALL
     */
    public function knockBack(Entity $attacker, float $damage, float $x, float $z, float $base = 0.4): void {
        $ffa = $this->getFfaHandler()->getFfaByLevel($attacker->getLevel());
        if (!is_null($ffa)) {
            [$kbXZ, $kbY, $kbHitDelay, $kbHeightLimit] = $this->getFfaHandler()->getKnockback($ffa);
            [$playerY, $damagerY] = [$this->getY(), $attacker->getY()];
            $kbY = ($playerY > $damagerY && $playerY - $damagerY >= $kbHeightLimit) ? $kbY / 1.25 : $kbY;
        }
        $f = sqrt($x * $x + $z * $z);
        if ($f <= 0) {
            return;
        }
        if (mt_rand() / mt_getrandmax() > $this->getAttributeMap()->getAttribute(Attribute::KNOCKBACK_RESISTANCE)->getValue()) {
            $f = 1 / $f;
            $motion = clone $this->motion;
            $motion->x /= 2;
            $motion->y /= 2;
            $motion->z /= 2;
            $motion->x += ($x * $f * ($kbXZ ?? $base));
            $motion->y += $kbY ?? $base;
            $motion->z += ($z * $f * ($kbXZ ?? $base));
            if ($motion->y > $kbY) {
                $motion->y = $kbY;
            }
            $this->setMotion($motion);
        }
    }

    /**
     * @return void
     */
    public function prepare(): void {
        $this->setHealth($this->getMaxHealth());
        $this->getInventory()->clearAll();
        $this->getArmorInventory()->clearAll();
        $this->getOffHandInventory()->clearAll();
        $this->setGamemode(GameMode::SURVIVAL);
        $this->setAllowFlight(false);
        $this->setFlying(false);
        $this->setXpLevel(0);
        $this->setXpProgress(0.0);
        $this->setFood($this->getMaxFood());
        $this->setSaturation(20);
        $this->setImmobile(false);
        foreach ($this->getEffects() as $effect) {
            if ($this->hasEffect($effect->getId()) && $effect->getId() !== Effect::NIGHT_VISION) {
                $this->removeEffect($effect->getId());
            }
        }
    }

}
