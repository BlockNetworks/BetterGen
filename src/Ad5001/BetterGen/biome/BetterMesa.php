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

use Ad5001\BetterGen\populator\CactusPopulator;
use Ad5001\BetterGen\populator\DeadbushPopulator;
use Ad5001\BetterGen\populator\SugarCanePopulator;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\biome\SandyBiome;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\Ore;
use pocketmine\world\generator\populator\Tree;

class BetterMesa extends SandyBiome
{

	public function __construct()
	{
		parent::__construct();
		$deadBush = new DeadbushPopulator ();
		$deadBush->setBaseAmount(1);
		$deadBush->setRandomAmount(2);

		$cactus = new CactusPopulator ();
		$cactus->setBaseAmount(1);
		$cactus->setRandomAmount(2);

		$sugarCane = new SugarCanePopulator ();
		$sugarCane->setRandomAmount(20);
		$sugarCane->setBaseAmount(3);

		$sugarCane = new Tree();
		$sugarCane->setRandomAmount(2);
		$sugarCane->setBaseAmount(0);

		$ores = new Ore();
		$ores->setOreTypes([
			new OreType(VanillaBlocks::GOLD_ORE(), 2, 8, 0, 32)
		]);

		$this->addPopulator($cactus);
		$this->addPopulator($deadBush);
		$this->addPopulator($sugarCane);
		$this->addPopulator($ores);

		$this->setElevation(80, 83);

		$this->temperature = 0.8;
		$this->rainfall = 0;
		$this->setGroundCover([
			VanillaBlocks::DIRT(),
			VanillaBlocks::HARDENED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::HARDENED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::GRAY_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::HARDENED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::BROWN_STAINED_CLAY(),
			VanillaBlocks::BROWN_STAINED_CLAY(),
			VanillaBlocks::BROWN_STAINED_CLAY(),
			VanillaBlocks::RED_STAINED_CLAY(),
			VanillaBlocks::RED_STAINED_CLAY(),
			VanillaBlocks::RED_STAINED_CLAY(),
			VanillaBlocks::YELLOW_STAINED_CLAY(),
			VanillaBlocks::GRAY_STAINED_CLAY(),
			VanillaBlocks::WHITE_STAINED_CLAY(),
			VanillaBlocks::GRAY_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::HARDENED_CLAY(),
			VanillaBlocks::HARDENED_CLAY(),
			VanillaBlocks::HARDENED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::ORANGE_STAINED_CLAY(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE(),
			VanillaBlocks::RED_SANDSTONE()
		]);
	}

	public function getName(): string
	{
		return "BetterMesa";
	}

	public function getId(): int
	{
		return 39;
	}
}
