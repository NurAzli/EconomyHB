<?php

namespace EconomyHb;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    public function onEnable(): void {
        $this->saveDefaultConfig(); // Saves default config if config.yml doesn't exist.
        $this->getLogger()->info("EconomyPlugin has been enabled!");
    }

    public function giveSalary($player, $job, $amount) {
        $playerName = $player->getName();
        if (!$this->getConfig()->exists("economy.$playerName")) {
            $this->getConfig()->setNested("economy.$playerName", 0);
        }

        $economy = $this->getConfig()->getNested("economy.$playerName", 0);
        $economy += $amount;
        $this->getConfig()->setNested("economy.$playerName", $economy);
        $this->getConfig()->save();
    }

    public function getPlayerEconomy($player) {
        $playerName = $player->getName();
        return $this->getConfig()->getNested("economy.$playerName", 0);
    }
}
