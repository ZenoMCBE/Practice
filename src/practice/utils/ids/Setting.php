<?php

namespace practice\utils\ids;

interface Setting {

    public const HIDE_NON_OPPONENT = "hide-non-opponent";
    public const IMMEDIATE_RESPAWN = "immediate-respawn";
    public const LIGHTNING_KILL = "lightning-kill";
    public const NIGHT_VISION = "night-vision";
    public const SCOREBOARD = "scoreboard";

    public const ALL = [
        self::HIDE_NON_OPPONENT,
        self::IMMEDIATE_RESPAWN,
        self::LIGHTNING_KILL,
        self::NIGHT_VISION,
        self::SCOREBOARD
    ];

    public const NAME = [
        self::HIDE_NON_OPPONENT => "Hide Non-Opponents",
        self::IMMEDIATE_RESPAWN => "Immediate Respawn",
        self::LIGHTNING_KILL => "Lightning Kill",
        self::NIGHT_VISION => "Night Vision",
        self::SCOREBOARD => "Scoreboard"
    ];

}
