<?php

namespace practice\forms\childs;

use pocketmine\form\CustomFormResponse;
use pocketmine\form\element\Toggle;
use pocketmine\Player;
use pocketmine\utils\SingletonTrait;
use practice\forms\PracticeCustomForm;
use practice\handlers\HandlerTrait;
use practice\PPlayer;
use practice\utils\ids\Setting;

final class SettingsForms {

    use HandlerTrait, SingletonTrait;

    /**
     * @param PPlayer $player
     * @return PracticeCustomForm
     */
    public function getForm(PPlayer $player): PracticeCustomForm {
        $toggles = array_map(function (string $setting) use ($player) {
            $settingName = $this->getSettingsHandler()->getSettingName($setting);
            return new Toggle(
                strtolower(str_replace(" ", "-", $settingName)),
                $settingName,
                $this->getSettingsHandler()->has($player, $setting)
            );
        }, $this->getSettingsHandler()->getSettings());
        $onSubmit = function (Player $player, CustomFormResponse $data): void {
            $changes = [];
            assert($player instanceof PPlayer);
            [$hideNonOpponents, $immediateRespawn, $lightningKill, $nightVision, $scoreboard] = [
                $data->getBool("hide-non-opponents"), $data->getBool("immediate-respawn"),
                $data->getBool("lightning-kill"), $data->getBool("night-vision"), $data->getBool("scoreboard")
            ];
            $settings = [
                Setting::HIDE_NON_OPPONENT => $hideNonOpponents,
                Setting::IMMEDIATE_RESPAWN => $immediateRespawn,
                Setting::LIGHTNING_KILL => $lightningKill,
                Setting::NIGHT_VISION => $nightVision,
                Setting::SCOREBOARD => $scoreboard
            ];
            foreach ($settings as $setting => $value) {
                if ($this->getSettingsHandler()->get($player, $setting) !== $value) {
                    $changes[$setting] = $value;
                }
                $this->getSettingsHandler()->set($player, $setting, $value);
            }
            if (!empty($changes)) {
                $player->sendMessage("§r§l§q» §r§aListe des changements de paramètre §l§q«");
                $state = fn (bool $value): string => $value ? "§aActivé" : "§cDésactivé";
                foreach ($changes as $setting => $value) {
                    $player->sendMessage("§l§q| §r§f" . $this->getSettingsHandler()->getSettingName($setting) . " §7-> " . $state($value));
                }
            }
            $player->givePreferences(true);
        };
        $onClose = function (Player $player): void {
            $player->sendMessage("§l§4» §r§cNOTE §l§4«");#
            $player->sendMessage("§l§4| §r§7Vous devez confirmer vos changements de paramètres pour qu'ils soient effectifs !");
        };
        return new PracticeCustomForm("§r§l§q» §r§aParamètres §l§q«", $toggles, $onSubmit, $onClose);
    }

}
