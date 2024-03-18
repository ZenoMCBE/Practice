<?php

namespace practice\utils;

use pocketmine\entity\Entity;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\Server;
use practice\PPlayer;
use practice\Practice;

final class Utils {

    public const PREFIX = "§l§q» §r";

    /**
     * @param string $name
     * @return PPlayer|null
     */
    public static function getPlayer(string $name): ?Player {
        $name = strtolower($name);
        $found = null;
        $delta = PHP_INT_MAX;
        foreach (Practice::getInstance()->getServer()->getLoggedInPlayers() as $player) {
            $curDelta = strpos(strtolower($player->getName()), $name);
            if ($curDelta === 0 || ($curDelta !== false && $curDelta < $delta && !is_null(($found = $player)))) {
                if ($curDelta === 0) {
                    return $player;
                }
                $delta = $curDelta;
            }
        }
        return $found;
    }

    /**
     * @param PPlayer|string $player
     * @param bool $upperCase
     * @param bool $lowerCase
     * @return string
     */
    public static function getPlayerName(PPlayer|string $player, bool $upperCase = false, bool $lowerCase = false): string {
        $playerName = $player instanceof PPlayer ? $player->getUpperName(true) : $player;
        $name = $upperCase ? str_replace(" ", "_", $playerName) : str_replace("_", " ", $playerName);
        return $lowerCase ? strtolower($name) : $name;
    }

    /**
     * @param array $array
     * @param int|null $page
     * @param int $separator
     * @return array
     */
    public static function arrayToPage(array $array, ?int $page, int $separator): array {
        return [ceil(count($array) / $separator), array_slice($array, ($page - 1) * $separator, $separator)];
    }

    /**
     * @param int $value
     * @param int $min
     * @param int $max
     * @param bool $reverse
     * @return float
     */
    public static function calculateNormalizedValue(int $value, int $min, int $max, bool $reverse = false): float {
        if ($max === $min || $value === $min) {
            return 0.0;
        } else if ($min > $max || $value === $max) {
            return 1.0;
        }
        $denominator = max($max - $min, 1);
        $normalizedValue = max(min(($value - $min) / $denominator, 1.0), 0.0);
        return $reverse ? (1.0 - $normalizedValue) : $normalizedValue;
    }

    /**
     * @param PPlayer $player
     * @return void
     */
    public static function doLightning(PPlayer $player): void {
        $entityId = Entity::$entityCount++;
        $position = $player->getPosition();
        $level = $player->getLevel();

        $light = new AddActorPacket();
        $light->type = "minecraft:lightning_bolt";
        $light->entityRuntimeId = $entityId;
        $light->metadata = [];
        $light->motion = null;
        $light->yaw = $player->getYaw();
        $light->pitch = $player->getPitch();
        $light->position = new Vector3($position->getX(), $position->getY(), $position->getZ());
        Server::getInstance()->broadcastPacket($level->getPlayers(), $light);

        $block = $level->getBlock($position->floor()->down());
        $particle = new DestroyBlockParticle($position, $block);
        $level->addParticle($particle);

        $sound = new PlaySoundPacket();
        $sound->soundName = "ambient.weather.thunder";
        $sound->x = $position->getX();
        $sound->y = $position->getY();
        $sound->z = $position->getZ();
        $sound->volume = 1;
        $sound->pitch = 1;
        Server::getInstance()->broadcastPacket($level->getPlayers(), $sound);
    }

}
