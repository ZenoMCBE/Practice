<?php

namespace practice\loaders;

interface ILoader {

    /**
     * @return void
     */
    public function onLoad(): void;

    /**
     * @return void
     */
    public function onUnload(): void;

}
