<?php

namespace practice\utils\ids;

interface Scoreboard {

    public const LOBBY = "lobby";
    public const FFA = "ffa";

    public const SEPARATOR = "\u{E000}";

    public const MINI_SEPARATOR = "\u{E141}";

    public const FORMAT = [
        self::LOBBY => [
            1 => self::SEPARATOR,
            2 => " §l§q{PLAYER_NAME}",
            3 => " " . self::MINI_SEPARATOR . " §fGrade§7: {RANK}",
            4 => "  ",
            5 => " §l§qServeur",
            6 => " " . self::MINI_SEPARATOR . " §fJoueur(s)§7: §c{COUNT}",
            7 => "§r" . self::SEPARATOR,
        ],
        self::FFA => [
            1 => self::SEPARATOR,
            2 => " §l§q{PLAYER_NAME}",
            3 => " " . self::MINI_SEPARATOR . " §fK§7: §a{KILL} §8| §fD§7: §c{DEATH}",
            4 => " " . self::MINI_SEPARATOR . " §fK/D§7: §e{KDR}",
            5 => "  ",
            6 => " §l§qInfos",
            7 => " " . self::MINI_SEPARATOR . " §fStatus§7: {STATUS}",
            8 => " " . self::MINI_SEPARATOR . " §fCombat§7: {COMBAT_TIME}",
            9 => "§r" . self::SEPARATOR,
        ]
    ];

    public const WORLD = [
        "lobby" => self::LOBBY,
        "nodebuff-one-ffa" => self::FFA,
        "nodebuff-two-ffa" => self::FFA,
        "nodebuff-nitro-ffa" => self::FFA,
        "soupfly-ffa" => self::FFA
    ];

}
