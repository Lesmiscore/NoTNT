<?php

namespace nao20010128nao;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

class TNTCommand extends Command implements PluginIdentifiableCommand
{
	private $enable = false;
	public function __construct(NoTNT $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		parent::__construct($name, $description);
	}
	
	private function checkPermission(CommandSender $sender){
		if(!($sender->isOp() or $sender->hasPermission("nt.commmand"))){
			$sender->sendMessage(TextFormat::RED . "You don't have permission to use this command.");
			return false;
		}
		return true;
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!isset($args[0]))
		{
			if(!$this->checkPermission($sender)) return true;
			$sender->sendMessage(TextFormat::GREEN . "[NoTNT] Usage: /deltnt <on|off|true|false|enable|disable>");
			return false;
		}

		switch($args[0]){
			case "on":
			case "true":
			case "enable":
				$this->plugin->config["deleteTNTs"]=true;
				$sender->sendMessage(TextFormat::GREEN."NoTNT can delete TNTs using TNT removing engine!");
				break;
			case "off":
			case "false":
			case "disable":
				$this->plugin->config["deleteTNTs"]=false;
				$sender->sendMessage(TextFormat::RED."NoTNT can't delete TNTs using TNT removing engine!");
				break;
			default:
				$sender->sendMessage(TextFormat::GREEN."[NoTNT] Usage: /deltnt <on|off|true|false|enable|disable>");
				break;
		}
		return true;
	}

	public function getPlugin(){
		return $this->plugin;
	}
}