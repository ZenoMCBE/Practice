<?php

namespace practice\forms;

use practice\forms\childs\{FfaForms, SettingsForms};

trait FormTrait {

    /**
     * @return FfaForms
     */
    public function getFfaForms(): FfaForms {
        return FfaForms::getInstance();
    }

    /**
     * @return SettingsForms
     */
    public function getSettingsForms(): SettingsForms {
        return SettingsForms::getInstance();
    }

}
