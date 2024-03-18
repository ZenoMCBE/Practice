<?php

namespace practice\utils\ids;

use pocketmine\entity\Effect;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\ItemIds;

interface Kit {

    public const LOBBY = "lobby";
    public const NODEBUFF = "nodebuff";
    public const SOUP = "soup";

    public const KIT = [
        self::LOBBY => [
            "armor" => [],
            "item" => [
                4 => [ItemIds::COMPASS, 0, "§r§l§q» §r§aFFA §l§q«\n§r§8(§7Clique-droit !§8)", 1, null, null]
            ],
            "effect" => []
        ],
        self::NODEBUFF => [
            "armor" => [
                ArmorInventory::SLOT_HEAD => [ItemIds::DIAMOND_HELMET, Enchantment::UNBREAKING, 2],
                ArmorInventory::SLOT_CHEST => [ItemIds::DIAMOND_CHESTPLATE, Enchantment::UNBREAKING, 2],
                ArmorInventory::SLOT_LEGS => [ItemIds::DIAMOND_LEGGINGS, Enchantment::UNBREAKING, 2],
                ArmorInventory::SLOT_FEET => [ItemIds::DIAMOND_BOOTS, Enchantment::UNBREAKING, 2],
            ],
            "item" => [
                0 => [ItemIds::DIAMOND_SWORD, 0, null, 1, Enchantment::UNBREAKING, 2],
                1 => [ItemIds::ENDER_PEARL, 0, null, 16, null, null],
                2 => [ItemIds::SPLASH_POTION, 22, null, 34, null, null]
            ],
            "effect" => [
                Effect::SPEED => [60*60*60*60, 0, false]
            ]
        ],
        self::SOUP => [
            "armor" => [
                ArmorInventory::SLOT_HEAD => [ItemIds::DIAMOND_HELMET, Enchantment::UNBREAKING, 2],
                ArmorInventory::SLOT_CHEST => [ItemIds::DIAMOND_CHESTPLATE, Enchantment::UNBREAKING, 2],
                ArmorInventory::SLOT_LEGS => [ItemIds::DIAMOND_LEGGINGS, Enchantment::UNBREAKING, 2],
                ArmorInventory::SLOT_FEET => [ItemIds::DIAMOND_BOOTS, Enchantment::UNBREAKING, 2],
            ],
            "item" => [
                0 => [ItemIds::DIAMOND_SWORD, 0, null, 1, Enchantment::UNBREAKING, 2],
                1 => [ItemIds::ENDER_PEARL, 0, null, 16, null, null],
                2 => [ItemIds::SLIME_BALL, 0, null, 64, null, null],
                3 => [ItemIds::GOLDEN_APPLE, 0, null, 4, null, null]
            ],
            "effect" => []
        ]
    ];

    public const WORLD = [
        "lobby" => self::LOBBY,
        "nodebuff-one-ffa" => self::NODEBUFF,
        "nodebuff-two-ffa" => self::NODEBUFF,
        "soupfly-ffa" => self::SOUP
    ];

    public const FFA = [
        FFA::NODEBUFF_ONE => self::NODEBUFF,
        FFA::NODEBUFF_TWO => self::NODEBUFF,
        FFA::SOUP => self::SOUP
    ];

}
