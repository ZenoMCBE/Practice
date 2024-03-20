<?php

namespace practice\handlers\childs;

use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\{RemoveObjectivePacket, SetDisplayObjectivePacket, SetScorePacket};
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use practice\datas\NoProviderData;
use practice\handlers\{HandlerTrait, IHandler};
use practice\PPlayer;
use practice\utils\ids\{Scoreboard, Setting, Statistic};

final class ScoreboardHandler implements IHandler, NoProviderData {

    use HandlerTrait, SingletonTrait;

    public const GLYPH_PER_LETTER = [
        'a' => "\u{E10D}",
        'b' => "\u{E10E}",
        'c' => "\u{E10F}",
        'd' => "\u{E110}",
        'e' => "\u{E111}",
        'f' => "\u{E112}",
        'g' => "\u{E113}",
        'h' => "\u{E114}",
        'i' => "\u{E115}",
        'j' => "\u{E116}",
        'k' => "\u{E117}",
        'l' => "\u{E118}",
        'm' => "\u{E119}",
        'n' => "\u{E11A}",
        'o' => "\u{E11B}",
        'p' => "\u{E11C}",
        'q' => "\u{E11D}",
        'r' => "\u{E11E}",
        's' => "\u{E11F}",
        't' => "\u{E120}",
        'u' => "\u{E121}",
        'v' => "\u{E122}",
        'w' => "\u{E123}",
        'x' => "\u{E124}",
        'y' => "\u{E125}",
        'z' => "\u{E126}",
    ];

    /**
     * @return string
     */
    public function getName(): string {
        return "Scoreboard";
    }

    /**
     * @return void
     */
    public function onEnable(): void {}

    /**
     * @param PPlayer $player
     * @param string $scoreboard
     * @param bool $update
     * @return void
     */
    public function sendScoreboard(PPlayer $player, string $scoreboard, bool $update = false): void {
        $player->setScoreboard($scoreboard);
        if ($this->getSettingsHandler()->has($player, Setting::SCOREBOARD)) {
            if ($update) {
                $this->removeScoreboard($player);
            }
            $this->changeTitle($player, $this->formatWordToGlyph("zeno practice"));
            $lines = $this->getScoreboardLines($scoreboard);
            foreach ($lines as $line => $content) {
                $formattedContent = $this->format($player, $content);
                $this->changeLine($player, $line, $formattedContent);
            }
        } else {
            $this->removeScoreboard($player);
        }
    }

    /**
     * @param PPlayer $player
     * @return void
     * @noinspection PhpUnused
     */
    public function removeScoreboard(PPlayer $player): void {
        $packet = new RemoveObjectivePacket();
        $packet->objectiveName = "objective";
        $player->sendDataPacket($packet);
    }

    /**
     * @param PPlayer $player
     * @param string $title
     * @return void
     */
    private function changeTitle(PPlayer $player, string $title): void {
        $packet = new SetDisplayObjectivePacket();
        $packet->displaySlot = "sidebar";
        $packet->objectiveName = "objective";
        $packet->displayName = $title;
        $packet->criteriaName = "dummy";
        $packet->sortOrder = 0;
        $player->sendDataPacket($packet);
    }

    /**
     * @param PPlayer $player
     * @param int $line
     * @param string $content
     * @return void
     */
    private function createLine(PPlayer $player, int $line, string $content): void {
        $packetEntry = new ScorePacketEntry();
        $packetEntry->objectiveName = "objective";
        $packetEntry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
        $packetEntry->customName = $content;
        $packetEntry->score = $line;
        $packetEntry->scoreboardId = $line;
        $packetScore = new SetScorePacket();
        $packetScore->type = SetScorePacket::TYPE_CHANGE;
        $packetScore->entries[] = $packetEntry;
        $player->sendDataPacket($packetScore);
    }

    /**
     * @param PPlayer $player
     * @param int $line
     * @return void
     */
    private function removeLine(PPlayer $player, int $line): void {
        $packetEntry = new ScorePacketEntry();
        $packetEntry->objectiveName = "objective";
        $packetEntry->score = $line;
        $packetEntry->scoreboardId = $line;
        $packetScore = new SetScorePacket();
        $packetScore->type = SetScorePacket::TYPE_REMOVE;
        $packetScore->entries[] = $packetEntry;
        $player->sendDataPacket($packetScore);
    }

    /**
     * @param PPlayer $player
     * @param int $line
     * @param string $content
     * @return void
     */
    public function changeLine(PPlayer $player, int $line, string $content): void {
        $this->removeLine($player, $line);
        $this->createLine($player, $line, $content);
    }

    /**
     * @param PPlayer $player
     * @param string $content
     * @return string
     */
    public function format(PPlayer $player, string $content): string {
        $rank = $this->getRanksHandler()->get($player);
        $formatttedRank = $this->getRanksHandler()->getColorByRank($rank) . ucfirst($rank);
        $onlinePlayers = count(Server::getInstance()->getLoggedInPlayers());
        $kill = $this->getStatisticsHandler()->get($player, Statistic::KILL);
        $death = $this->getStatisticsHandler()->get($player, Statistic::DEATH);
        $kdr = $this->getStatisticsHandler()->getKdr($player);
        $combatTime = $player->isInCombat() ? "§c" . $player->getCombatTime(true) : "§a0";
        return str_replace(
            ["{PLAYER_NAME}", "{RANK}", "{COUNT}", "{KILL}", "{DEATH}", "{KDR}", "{STATUS}", "{COMBAT_TIME}"],
            [$player->getName(), $formatttedRank, $onlinePlayers, $kill, $death, ($kdr ?? "N/A"), $player->getFormattedStatus(), $combatTime],
            $content
        );
    }

    /**
     * @param PPlayer $player
     * @return void
     */
    public function updateRank(PPlayer $player): void {
        if ($player->getScoreboard() == Scoreboard::LOBBY) {
            $content = $this->format($player, $this->getScoreboardLines(Scoreboard::LOBBY)[3]);
            $this->changeLine($player, 3, $content);
        }
    }

    /**
     * @param PPlayer $player
     * @return void
     */
    public function updateOnlinePlayers(PPlayer $player): void {
        if ($player->getScoreboard() == Scoreboard::LOBBY) {
            $content = $this->format($player, $this->getScoreboardLines(Scoreboard::LOBBY)[6]);
            $this->changeLine($player, 6, $content);
        }
    }

    /**
     * @param PPlayer $player
     * @return void
     */
    public function updateStatistics(PPlayer $player): void {
        if ($player->getScoreboard() == Scoreboard::FFA) {
            foreach ([3, 4] as $line) {
                $content = $this->format($player, $this->getScoreboardLines(Scoreboard::FFA)[$line]);
                $this->changeLine($player, $line, $content);
            }
        }
    }

    /**
     * @param PPlayer $player
     * @return void
     */
    public function updateStatus(PPlayer $player): void {
        if ($player->getScoreboard() == Scoreboard::FFA) {
            $content = $this->format($player, $this->getScoreboardLines(Scoreboard::FFA)[7]);
            $this->changeLine($player, 7, $content);
        }
    }

    /**
     * @param PPlayer $player
     * @return void
     */
    public function updateCombatTime(PPlayer $player): void {
        if ($player->getScoreboard() == Scoreboard::FFA) {
            $content = $this->format($player, $this->getScoreboardLines(Scoreboard::FFA)[8]);
            $this->changeLine($player, 8, $content);
        }
    }

    /**
     * @param string $scoreboard
     * @return array
     */
    public function getScoreboardLines(string $scoreboard): array {
        return Scoreboard::FORMAT[$scoreboard];
    }

    /**
     * @param Level $level
     * @return string|null
     */
    public function getScoreboardByLevel(Level $level): ?string {
        return Scoreboard::WORLD[$level->getFolderName()] ?? null;
    }

    /**
     * @param string $word
     * @return string
     */
    private function formatWordToGlyph(string $word): string {
        return implode('', array_map(fn (string $letter) => $this->getGlyphByLetter($letter), str_split($word)));
    }

    /**
     * @param string $letter
     * @return string
     */
    private function getGlyphByLetter(string $letter): string {
        return self::GLYPH_PER_LETTER[$letter] ?? " ";
    }

    /**
     * @return void
     */
    public function onDisable(): void {}

}
