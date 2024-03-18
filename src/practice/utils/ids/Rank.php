<?php

namespace practice\utils\ids;

interface Rank {

    public const PLAYER = 'player';
    public const PLUS = '+';
    public const MEDIA = 'media';
    public const STAFF = 'staff';
    public const ADMIN = 'admin';
    public const OWNER = 'owner';

    public const ALL = [
        self::PLAYER,
        self::PLUS,
        self::MEDIA,
        self::STAFF,
        self::ADMIN,
        self::OWNER
    ];

}
