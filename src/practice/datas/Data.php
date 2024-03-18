<?php

namespace practice\datas;

interface Data {

    /**
     * @return void
     */
    public function loadCache(): void;

    /**
     * @return array
     */
    public function getCache(): array;

    /**
     * @return void
     */
    public function unloadCache(): void;

}
