<?php

namespace practice\forms;

use practice\forms\childs\FfaForms;

trait FormTrait {

    /**
     * @return FfaForms
     */
    public function getFfaForms(): FfaForms {
        return FfaForms::getInstance();
    }

}
