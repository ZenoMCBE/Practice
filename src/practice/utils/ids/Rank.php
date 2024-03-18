<?php

namespace practice\utils\ids;

interface Rank {

    public const PLAYER = 'player';
    public const STAR = 'star';
    public const MEDIA = 'media';
    public const STAFF = 'staff';
    public const ADMIN = 'admin';
    public const OWNER = 'owner';

    public const ALL = [
        self::PLAYER,
        self::STAR,
        self::MEDIA,
        self::STAFF,
        self::ADMIN,
        self::OWNER
    ];

}
