<?php

namespace nao20010128nao;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\block\Block;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

class NoTNT extends PluginBase implements Listener
{
	public $config,$console;
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(file_exists($this->getDataFolder()."/config.yml")){
			$this->config=yaml_parse_file($this->getDataFolder()."/config.yml");
		}else{
			$this->config=array("banTNT"=>true,"deleteTNTs"=>true);
		}
		$this->console=new ConsoleCommandSender();
		$commandMap = $this->getServer()->getCommandMap();
		$commandMap->register(
			"tnt", 
			new TNTCommand($this, "tnt", "Manages TNT could be place by players.")
		);
		$commandMap->register(
			"deltnt", 
			new DelTNTCommand($this, "deltnt", "Manages TNT could be deleted by TNT removing engine.")
		);
		if($this->getServer()->getPluginManager()->getPlugin("NoExplode")!=null){
			$this->console->sendMessage("[NoTNT] NoExplode has detected! Is it needed?");
		}
	}
	public function onDisable(){
		yaml_emit_file($this->getDataFolder()."/config.yml",$this->config);
	}
	public function onPlayerInteract(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$username = $player->getName();
		if($this->config["banTNT"] and ($event->getItem()->getId()==46)){
			$event->setCancelled(true);
			$this->console->sendMessage("[NoTNT] ".TextFormat::RED."TNT has placed by ".$username.".");
			$player->sendMessage(TextFormat::RED."You can't place TNTs!");
		}
	}
	public function onPlayerInteract2(BlockBreakEvent $event){
		$block=$event->getBlock();
		$player=$event->getPlayer();
		if($this->config["deleteTNTs"] and ($block->getId()==46)){
			$this->console->sendMessage("[NoTNT] ".TextFormat::GREEN."A TNT was broken. Deleting TNTs around it...");
			$player->sendMessage(TextFormat::GREEN."You broke a TNT. Deleting TNTs around it...");
			$this->removeTNTrescursive($player->getPosition()->getLevel()->getName(),$block->getX(),$block->getY(),$block->getZ(),0);
			$this->console->sendMessage("[NoTNT] ".TextFormat::GREEN."Complete!");
			$player->sendMessage(TextFormat::GREEN."Complete!");
		}
	}
	public function onExplode(EntityExplodeEvent $event){
		$event->setCancelled(true);// prevent 100%
		$fld=$event->getEntity()->getLevel()->getFolderName();
		$x=$event->getPosition()->getX();
		$y=$event->getPosition()->getY();
		$z=$event->getPosition()->getZ();
		$this->console->sendMessage("[NoTNT] ".TextFormat::RED."An explosion was prevented at:$fld ($x,$y,$z)");
	}
	public function onExplode2(ExplosionPrimeEvent $event){
		$event->setCancelled(true);
		$fld=$event->getEntity()->getLevel()->getFolderName();
		$x=$event->getEntity()->getPosition()->getX();
		$y=$event->getEntity()->getPosition()->getY();
		$z=$event->getEntity()->getPosition()->getZ();
		$this->console->sendMessage("[NoTNT] ".TextFormat::RED."An explosion was prevented at:$fld ($x,$y,$z)");
	}
	public function removeTNTrescursive($levelName,$x,$y,$z,$nest=0){
		$level=$this->getServer()->getLevelByName($levelName);
		if($level==null){
			return;
		}
		$vector=new Vector3($x,$y,$z);
		$block=$level->getBlock($vector,false);
		if($block->getId()==46){
			if(!$level->setBlock($vector,Block::get(0))){
				$this->console->sendMessage("[NoTNT] ".TextFormat::RED."TNT at ($x,$y,$z) couldn't be deleted!.");
			}
		}else{
			return;
		}
		if($nest>=30){
			return;
		}
		
		$this->removeTNTrescursive($levelName,$x-2,$y-2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y-2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y-2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y-2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y-2,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y-1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y-1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y-1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y-1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y-1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y  ,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y  ,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y  ,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y  ,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y  ,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-2,$y+2,$z+2,$nest+1);
		
		$this->removeTNTrescursive($levelName,$x-1,$y-2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y-2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y-2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y-2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y-2,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y-1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y-1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y-1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y-1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y-1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y  ,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y  ,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y  ,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y  ,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y  ,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x-1,$y+2,$z+2,$nest+1);
		
		$this->removeTNTrescursive($levelName,$x  ,$y-2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y-2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y-2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y-2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y-2,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y-1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y-1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y-1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y-1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y-1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y  ,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y  ,$z-1,$nest+1);
		/*               Don't need to check the same place               */
		$this->removeTNTrescursive($levelName,$x  ,$y  ,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y  ,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x  ,$y+2,$z+2,$nest+1);
		
		$this->removeTNTrescursive($levelName,$x+1,$y-2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y-2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y-2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y-2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y-2,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y-1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y-1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y-1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y-1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y-1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y  ,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y  ,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y  ,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y  ,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y  ,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+1,$y+2,$z+2,$nest+1);
		
		$this->removeTNTrescursive($levelName,$x+2,$y-2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y-2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y-2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y-2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y-2,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y-1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y-1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y-1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y-1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y-1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y  ,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y  ,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y  ,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y  ,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y  ,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+1,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+1,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+1,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+1,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+1,$z+2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+2,$z-2,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+2,$z-1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+2,$z  ,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+2,$z+1,$nest+1);
		$this->removeTNTrescursive($levelName,$x+2,$y+2,$z+2,$nest+1);
	}
}