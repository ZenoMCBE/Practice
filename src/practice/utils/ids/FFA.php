<?php

namespace practice\utils\ids;

interface FFA {

    public const NODEBUFF_ONE = "nodebuff-one";
    public const NODEBUFF_TWO = "nodebuff-two";
    public const NODEBUFF_NITRO = "nodebuff-nitro";
    public const SOUP = "soup";

    public const ALL = [
        self::NODEBUFF_ONE,
        self::NODEBUFF_TWO,
        self::NODEBUFF_NITRO,
        self::SOUP
    ];

    public const NAME = [
        self::NODEBUFF_ONE => "NoDebuff #1",
        self::NODEBUFF_TWO => "NoDebuff #2",
        self::NODEBUFF_NITRO => "NoDebuff Nitro",
        self::SOUP => "SoupFly"
    ];

    public const IMAGE = [
        self::NODEBUFF_ONE => "textures/items/potion_bottle_splash_heal.png",
        self::NODEBUFF_TWO => "textures/items/potion_bottle_splash_heal.png",
        self::NODEBUFF_NITRO => "textures/items/feather.png",
        self::SOUP => "textures/items/mushroom_stew.png"
    ];

    public const POSITION = [
        self::NODEBUFF_ONE => [
            "x" => [1030, 1100],
            "y" => 7,
            "z" => [5020, 5160]
        ],
        self::NODEBUFF_TWO => [
            "x" => [1030, 1100],
            "y" => 7,
            "z" => [5020, 5160]
        ],
        self::NODEBUFF_NITRO => [
            "x" => [1030, 1100],
            "y" => 7,
            "z" => [5020, 5160]
        ],
        self::SOUP => [
            "x" => [2910, 2990],
            "y" => 53,
            "z" => [110, 190],
        ]
    ];

    public const FFA = [
        "nodebuff-one-ffa" => self::NODEBUFF_ONE,
        "nodebuff-two-ffa" => self::NODEBUFF_TWO,
        "nodebuff-nitro-ffa" => self::NODEBUFF_NITRO,
        "soupfly-ffa" => self::SOUP
    ];

    public const LEVEL = [
        self::NODEBUFF_ONE => "nodebuff-one-ffa",
        self::NODEBUFF_TWO => "nodebuff-two-ffa",
        self::NODEBUFF_NITRO => "nodebuff-nitro-ffa",
        self::SOUP => "soupfly-ffa"
    ];

}
