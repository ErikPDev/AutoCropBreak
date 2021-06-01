<?php

/*
 *                _         _____                 ____                 _    
 *     /\        | |       / ____|               |  _ \               | |   
 *    /  \  _   _| |_ ___ | |     _ __ ___  _ __ | |_) |_ __ ___  __ _| | __
 *   / /\ \| | | | __/ _ \| |    | '__/ _ \| '_ \|  _ <| '__/ _ \/ _` | |/ /
 *  / ____ \ |_| | || (_) | |____| | | (_) | |_) | |_) | | |  __/ (_| |   < 
 * /_/    \_\__,_|\__\___/ \_____|_|  \___/| .__/|____/|_|  \___|\__,_|_|\_\
 *                                         | |                              
 *                                         |_| By @ErikPDev for PMMP
 *
 * AutoCropBreak, a crop breaker plugin for PocketMine-MP
 * Copyright (c) 2021 ErikPDev  < https://github.com/ErikPDev >
 *
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * VoteParty is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace ErikPDev\AutoCropBreak;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\block\{
    Sugarcane,
    Cactus,
    Air
};
class Main extends PluginBase implements Listener{
    private $base;
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->reloadConfig();
    }

    public function onGrow(\pocketmine\event\block\BlockGrowEvent $event){
        // if(!$event->getNewState() instanceof Sugarcane){return;}
        $MaxGrowBlocks = $this->getConfig()->get("MaxGrowBlocks");
        $X = $event->getBlock()->asPosition()->x;
        $Y = (int) $event->getBlock()->asPosition()->y;
        $Z = $event->getBlock()->asPosition()->z;

        // We'll start by getting the base of the Sugarcane. The blocks under it could be anything so I'll code it to be anything other than sugarcane.
        // We don't know the height of the sugar cane so we'll just go downwards from the sugar.
            
        for ($i = $Y; ; $i--) {
            if ($i < 0) {break;} // This will most likey not happen.
            if ($i > 256){break;} // Safety first!
            if($event->getBlock()->getLevelNonNull()->getBlock(new \pocketmine\math\Vector3($X, $i, $Z)) instanceof Air){continue;}
            if( !($event->getBlock()->getLevelNonNull()->getBlock(new \pocketmine\math\Vector3($X, $i, $Z)) instanceof SugarCane) ){ // This was easier than I thought
                $this->getLogger()->debug("Base found!".(string)$i);
                $this->base = $i; // We have found the base atlast, now we'll need to get the height of the Sugarcane by going upwards till we reach air
                $f = $i+1;
                for($c = $f; ; $c++){
                    if($event->getBlock()->getLevelNonNull()->getBlock(new \pocketmine\math\Vector3($X, $c, $Z)) instanceof SugarCane){continue;} // Continue the loop if it's a sugarcane
                    if($c-$this->base > $MaxGrowBlocks){
                        $event->setCancelled();
                        $event->getBlock()->getLevelNonNull()->dropItem(new \pocketmine\math\Vector3($X, $Y, $Z), \pocketmine\item\Item::get(338, 0, 1));
                    }
                    $this->getLogger()->debug("Top found!".(string)$c);
                    break;
                }
                break;
            }

        }
    
        for ($i = $Y; ; $i--) {
            if ($i < 0) {break;} // This will most likey not happen.
            if ($i > 256){break;} // Safety first!
            if($event->getBlock()->getLevelNonNull()->getBlock(new \pocketmine\math\Vector3($X, $i, $Z)) instanceof Air){continue;}
            if( !($event->getBlock()->getLevelNonNull()->getBlock(new \pocketmine\math\Vector3($X, $i, $Z)) instanceof Cactus) ){ // This was easier than I thought
                $this->getLogger()->debug("Base found!".(string)$i);
                $this->base = $i; // We have found the base atlast, now we'll need to get the height of the Cactus
                $f = $i+1;
                for($c = $f; ; $c++){
                    if($event->getBlock()->getLevelNonNull()->getBlock(new \pocketmine\math\Vector3($X, $c, $Z)) instanceof Cactus){continue;} // Continue the loop if it's a sugarcane
                    if($c-$this->base > $MaxGrowBlocks){
                        $event->setCancelled();
                        $event->getBlock()->getLevelNonNull()->dropItem(new \pocketmine\math\Vector3($X, $Y, $Z), \pocketmine\item\Item::get(81, 0, 1));
                    }
                    $this->getLogger()->debug("Top found!".(string)$c);
                    break;
                }
                break;
            }

        }

        

    }

}
