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

    public function onEnable() {
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

class EconomyCommand extends Command {

    private $plugin;

    public function __construct(EconomyHb $plugin) {
        parent::__construct("economy", "Manage player economy", null, ["eco"]);
        $this->setPermission("economyhb.command.economy");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            if (count($args) < 1) {
                $sender->sendMessage("Usage: /economy <add|subtract|check> [amount] [player]");
                return false;
            }

            $subCommand = strtolower($args[0]);
            $amount = isset($args[1]) ? (int) $args[1] : 0;

            switch ($subCommand) {
                case "add":
                    $player = isset($args[2]) ? $this->plugin->getServer()->getPlayer($args[2]) : $sender;
                    if ($player instanceof Player) {
                        $this->plugin->giveSalary($player, "Admin", $amount);
                        $sender->sendMessage("Added $amount to {$player->getName()}'s economy.");
                    } else {
                        $sender->sendMessage("Player not found.");
                    }
                    break;

                case "subtract":
                    $player = isset($args[2]) ? $this->plugin->getServer()->getPlayer($args[2]) : $sender;
                    if ($player instanceof Player) {
                        if ($this->plugin->takeMoney($player, $amount)) {
                            $sender->sendMessage("Subtracted $amount from {$player->getName()}'s economy.");
                        } else {
                            $sender->sendMessage("{$player->getName()}'s economy doesn't have enough money.");
                        }
                    } else {
                        $sender->sendMessage("Player not found.");
                    }
                    break;

                case "check":
                    $player = isset($args[1]) ? $this->plugin->getServer()->getPlayer($args[1]) : $sender;
                    if ($player instanceof Player) {
                        $economy = $this->plugin->checkPlayerEconomy($player);
                        $sender->sendMessage("{$player->getName()}'s economy: $economy");
                    } else {
                        $sender->sendMessage("Player not found.");
                    }
                    break;

                default:
                    $sender->sendMessage("Unknown sub-command. Usage: /economy <add|subtract|check> [amount] [player]");
                    return false;
            }

            return true;
        } else {
            $sender->sendMessage("This command can only be used in-game.");
            return false;
        }
    }
}
