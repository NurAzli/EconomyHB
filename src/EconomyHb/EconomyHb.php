<?php

namespace EconomyHb;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EconomyHb extends PluginBase {

    /** @var Config */
    private $config;

    public function onEnable():void {
        $this->getLogger()->info("EconomyHb has been enabled!");

        // Pastikan direktori plugin_data/NurAzli/EconomyHb/ ada
        $this->saveResource("config.yml");

        // Inisialisasi konfigurasi
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        // Register command
        $this->getServer()->getCommandMap()->register("economyhb", new CheckEconomyCommand($this));
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

class CheckEconomyCommand extends Command {

    private $plugin;

    public function __construct(EconomyHb $plugin) {
        parent::__construct("checkeconomy", "Check your current economy", null, ["ce"]);
        $this->setPermission("economyhb.command.checkeconomy");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            $this->plugin->checkPlayerEconomy($sender);
            return true;
        } else {
            $sender->sendMessage("This command can only be used in-game.");
            return false;
        }
    }
}
