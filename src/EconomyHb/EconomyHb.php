<?php

namespace NurAzli\EconomyHb;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class EconomyHb extends PluginBase {

    /** @var Config */
    private $config;

    public function onEnable() {
        $this->getLogger()->info("EconomyHb has been enabled!");

        // Pastikan direktori plugin_data/NurAzli/EconomyHb/ ada
        $this->saveResource("config.yml");

        // Inisialisasi konfigurasi
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    public function giveSalary($player, $job, $amount) {
        $playerName = $player->getName();
        $economy = $this->config->getNested("economy.$playerName", 0);
        $economy += $amount;
        $this->config->setNested("economy.$playerName", $economy);
        $this->config->save();
    }

    public function getPlayerEconomy($player) {
        $playerName = $player->getName();
        return $this->config->getNested("economy.$playerName", 0);
    }
}
