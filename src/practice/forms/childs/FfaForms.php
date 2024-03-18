<?php

namespace practice\forms\childs;

use pocketmine\form\{CustomFormResponse, FormIcon, MenuOption};
use pocketmine\form\element\Input;
use pocketmine\Player;
use pocketmine\utils\SingletonTrait;
use practice\forms\{PracticeCustomForm, PracticeSimpleForm};
use practice\handlers\HandlerTrait;
use practice\PPlayer;
use practice\utils\ids\FFA;
use practice\utils\Utils;

final class FfaForms {

    use HandlerTrait, SingletonTrait;

    /**
     * @return PracticeSimpleForm
     */
    public function getFfaTeleportForm(): PracticeSimpleForm {
        $ffaList = array_map(function (string $ffa): MenuOption {
            $name = "§8" . $this->getFfaHandler()->getFfaName($ffa) . "\n§8" . $this->getFfaHandler()->countFfaPlayers($ffa) . " joueur(s)";
            $icon = new FormIcon($this->getFfaHandler()->getFfaImage($ffa), FormIcon::IMAGE_TYPE_PATH);
            return new MenuOption($name, $icon);
        }, $this->getFfaHandler()->getFFAs());
        $onSubmit = function (Player $player, int $selectedOption): void {
            $ffa = FFA::ALL[$selectedOption] ?? null;
            if (!is_null($ffa)) {
                assert($player instanceof PPlayer);
                $this->getFfaHandler()->teleport($player, $ffa);
            }
        };
        return new PracticeSimpleForm(
            "§r§l§q» §r§aFFA §l§q«",
            Utils::PREFIX . "§fVeuillez sélectionner une arène FFA pour s'y téléporter !",
            $ffaList,
            $onSubmit
        );
    }

    /**
     * @return PracticeSimpleForm
     */
    public function getFfaListForm(): PracticeSimpleForm {
        $ffaList = array_map(function (string $ffa): MenuOption {
            $name = "§8" . $this->getFfaHandler()->getFfaName($ffa);
            $icon = new FormIcon($this->getFfaHandler()->getFfaImage($ffa), FormIcon::IMAGE_TYPE_PATH);
            return new MenuOption($name, $icon);
        }, $this->getFfaHandler()->getFFAs());
        $onSubmit = function (Player $player, int $selectedOption): void {
            $ffa = FFA::ALL[$selectedOption] ?? null;
            if (!is_null($ffa)) {
                $player->sendForm($this->getFfaKnockbackInformationsForm($ffa));
            }
        };
        return new PracticeSimpleForm(
            "§r§l§q» §r§aFFA §l§q«",
            Utils::PREFIX . "§fVeuillez sélectionner une arène FFA pour y modifier ses knockbacks !",
            $ffaList,
            $onSubmit
        );
    }

    /**
     * @param string $ffa
     * @return PracticeCustomForm
     */
    public function getFfaKnockbackInformationsForm(string $ffa): PracticeCustomForm {
        [$xz, $y, $hitDelay, $heightLimit] = $this->getFfaHandler()->getKnockback($ffa);
        $onSubmit = function (Player $player, CustomFormResponse $data) use ($ffa): void {
            [$newXZ, $newY] = [$data->getString("xz"), $data->getString("y")];
            [$newHitDelay, $newHeightLimit] = [$data->getString("hit-delay"), $data->getString("height-limit")];
            if (is_numeric($newXZ) && is_numeric($newY) && is_numeric($newHitDelay) && is_numeric($newHeightLimit)) {
                $this->getFfaHandler()->setKnockback($ffa, floatval($newXZ), floatval($newY), intval($newHitDelay), floatval($newHeightLimit));
                $player->sendMessage(Utils::PREFIX . "§fVous venez de modifier les knockbacks de l'arène FFA §a" . $this->getFfaHandler()->getFfaName($ffa) . " §f! §a(XZ: " . $newXZ . " | Y: " . $newY . " | HIT-DELAY: " . $newHitDelay . " | HEIGHT-LIMIT: " . $newHeightLimit . ")");
            } else {
                $player->sendMessage(Utils::PREFIX . "§cLes valeurs que vous avez indiqué ne sont pas numériques.");
            }
        };
        return new PracticeCustomForm(
            "§r§l§q» §r§a" . $this->getFfaHandler()->getFfaName($ffa) . " FFA §l§q«",
            [
                new Input("xz", "XZ", defaultText: strval($xz)),
                new Input("y", "Y", defaultText: strval($y)),
                new Input("hit-delay", "Hit Delay", defaultText: strval($hitDelay)),
                new Input("height-limit", "Height Limit", defaultText: strval($heightLimit))
            ],
            $onSubmit
        );
    }

}
