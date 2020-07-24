<?php

/**
 *  ____             __     __                    ____                       
 * /\  _`\          /\ \__ /\ \__                /\  _`\                     
 * \ \ \L\ \     __ \ \ ,_\\ \ ,_\     __   _ __ \ \ \L\_\     __     ___    
 *  \ \  _ <'  /'__`\\ \ \/ \ \ \/   /'__`\/\`'__\\ \ \L_L   /'__`\ /' _ `\  
 *   \ \ \L\ \/\  __/ \ \ \_ \ \ \_ /\  __/\ \ \/  \ \ \/, \/\  __/ /\ \/\ \ 
 *    \ \____/\ \____\ \ \__\ \ \__\\ \____\\ \_\   \ \____/\ \____\\ \_\ \_\
 *     \/___/  \/____/  \/__/  \/__/ \/____/ \/_/    \/___/  \/____/ \/_/\/_/
 * Tomorrow's pocketmine generator.
 * @author Ad5001 <mail@ad5001.eu>, XenialDan <https://github.com/thebigsmileXD>
 * @link https://github.com/Ad5001/BetterGen
 * @category World Generator
 * @api 3.0.0
 * @version 1.1
 */

namespace Ad5001\BetterGen\biome;

use Ad5001\BetterGen\generator\BetterNormal;
use Ad5001\BetterGen\Main;
use pocketmine\block\utils\TreeType;
use pocketmine\world\biome\Biome;
use pocketmine\world\biome\ForestBiome;
use pocketmine\world\generator\populator\TallGrass;
use pocketmine\world\generator\populator\Tree;

class BetterForest extends ForestBiome implements Mountainable
{
	private $type;

	public function __construct(?TreeType $type = null, array $infos = [0.6, 0.5])
	{
		parent::__construct($type);
		$this->clearPopulators();
		
		$this->type = $type;

		$trees = new Tree($type);
		$trees->setBaseAmount(5);
		$this->addPopulator($trees);
		
		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(3);
		$this->addPopulator($tallGrass);
		
		$this->setElevation(63, 69);
		
		$this->temperature = $infos[0];
		$this->rainfall = $infos[1];
	}

	public function getId(): int
	{
		return $this->type->id();
	}
}
