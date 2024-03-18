<?php

namespace practice\datas;

use practice\PPlayer;

interface DefaultData {

    /**
     * @param PPlayer $player
     * @return void
     */
    public function setDefaultData(PPlayer $player): void;

    /**
     * @return mixed
     */
    public function getDefaultData(): mixed;

}
