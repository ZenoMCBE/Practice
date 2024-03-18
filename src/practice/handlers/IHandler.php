<?php

namespace practice\handlers;

interface IHandler {

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return void
     */
    public function onEnable(): void;

    /**
     * @return void
     */
    public function onDisable(): void;

}
