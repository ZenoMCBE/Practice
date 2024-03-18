<?php

namespace practice\handlers\childs;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\utils\SingletonTrait;
use practice\datas\NoProviderData;
use practice\handlers\HandlerTrait;
use practice\handlers\IHandler;
use practice\PPlayer;
use practice\utils\ids\Kit;

final class KitsHandler implements IHandler, NoProviderData {

    use HandlerTrait, SingletonTrait;

    /**
     * @return string
     */
    public function getName(): string {
        return "Kits";
    }

    /**
     * @return void
     */
    public function onEnable(): void {}

    /**
     * @param PPlayer $player
     * @param string $kit
     * @return void
     */
    public function send(PPlayer $player, string $kit): void {
        $player->prepare();
        $data = $this->getItems($kit);
        [$armors, $items, $effects] = [$data["armor"], $data["item"], $data["effect"]];
        foreach ($armors as $slot => $armorData) {
            [$id, $enchantment, $level] = $armorData;
            $armor = ItemFactory::get($id);
            assert($armor instanceof Armor);
            if (!is_null($enchantment) && !is_null($level)) {
                $armor->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchantment), $level));
            }
            $armor->setUnbreakable();
            $player->getArmorInventory()->setItem($slot, $armor);
        }
        foreach ($items as $slot => $itemData) {
            [$id, $meta, $name, $count, $enchantment, $level] = $itemData;
            $item = ItemFactory::get($id, $meta, $count)->setCustomName($name ?? "");
            if (!is_null($enchantment) && !is_null($level)) {
                $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment($enchantment), $level));
            }
            if ($id === ItemIds::SPLASH_POTION) {
                $player->getInventory()->addItem($item); // fuck PM3 pk ça stack les potions ?
            } else {
                $player->getInventory()->setItem($slot, $item);
            }
        }
        foreach ($effects as $effectId => $effectData) {
            [$duration, $amplifier, $visible] = $effectData;
            $player->addEffect(new EffectInstance(Effect::getEffect($effectId), $duration, $amplifier, $visible));
        }
    }

    /**
     * @param Level $level
     * @return string|null
     */
    public function getKitByLevel(Level $level): ?string {
        return Kit::WORLD[$level->getFolderName()] ?? null;
    }

    /**
     * @param string $ffa
     * @return string|null
     */
    public function getKitByFfa(string $ffa): ?string {
        return Kit::FFA[$ffa] ?? null;
    }

    /**
     * @param string $kit
     * @return array
     */
    private function getItems(string $kit): array {
        return Kit::KIT[$kit];
    }

    /**
     * @return void
     */
    public function onDisable(): void {}

}
