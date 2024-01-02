<?php

namespace NurAzli\Plugin\Stable;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EcomoHb extends PluginBase {

    /** @var Config */
    private $config;

    public function onEnable(): void {
        $this->getLogger()->info("EconomyHb has been enabled!");

        // Pastikan direktori plugin_data/EconomyHb/ ada
        $this->saveResource("config.yml");

        // Inisialisasi konfigurasi
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        // Register command
        $this->getServer()->getCommandMap()->register("economyhb", new EconomyCommand($this));
    }

    public function giveSalary(Player $player, $job, $amount) {
        $playerName = $player->getName();
        $economy = $this->config->getNested("economy.$playerName", 0);
        $economy += $amount;
        $this->config->setNested("economy.$playerName", $economy);
        $this->config->save();
    }

    public function takeMoney(Player $player, $amount) {
        $playerName = $player->getName();
        $economy = $this->config->getNested("economy.$playerName", 0);

        if ($economy >= $amount) {
            $economy -= $amount;
            $this->config->setNested("economy.$playerName", $economy);
            $this->config->save();
            return true; // Berhasil mengurangkan uang
        } else {
            return false; // Uang tidak cukup
        }
    }

    public function checkPlayerEconomy(Player $player) {
        $playerName = $player->getName();
        return $this->config->getNested("economy.$playerName", 0);
    }
}
