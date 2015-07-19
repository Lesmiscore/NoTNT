<?php

namespace nao20010128nao;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class NoTNT extends PluginBase implements Listener
{
	public $config,$console;
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(file_exists($this->getDataFolder()."/config.yml")){
			$this->config=yaml_parse_file($this->getDataFolder()."/config.yml");
		}else{
			$this->config=array("banTNT"=>true);
		}
		$this->console=new ConsoleCommandSender();
		$commandMap = $this->getServer()->getCommandMap();
		$commandMap->register(
			"tnt", 
			new TNTCommand($this, "tnt", "Manages TNT could be place by players.")
		);
		if($this->getServer()->getPluginManager()->getPlugin("NoExplode")!=null){
			$this->console->sendMessage("NoExplode has detected! Is it needed?");
		}
	}
	public function onDisable(){
		yaml_emit_file($this->getDataFolder()."/config.yml",$this->config);
	}
	public function onPlayerInteract(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		if($this->config["banTNT"] and ($event->getBlockAgainst()->getId()===46)){
			$event->setCancelled(true);
			$this->console->sendMessage("[NoTNT] ".TextFormat::RED."TNT has placed by ".$username.".");
			$player->sendMessage(TextFormat::RED."You can't place TNTs!");
		}
	}
}