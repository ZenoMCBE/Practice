<?php

namespace practice\utils\ids;

interface Statistic {

    public const KILL = "kill";
    public const DEATH = "death";

    public const ALL = [
        self::KILL,
        self::DEATH
    ];

}
