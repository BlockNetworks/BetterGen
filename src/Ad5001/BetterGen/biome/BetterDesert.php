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
use pocketmine\world\biome\Biome;
use pocketmine\world\biome\SandyBiome;

class BetterDesert extends SandyBiome implements Mountainable
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

		$this->addPopulator($cactus);
		$this->addPopulator($deadBush);
		$this->addPopulator($sugarCane);

		$this->setElevation(63, 70);

		$this->temperature = 0.5;
		$this->rainfall = 0;
		$this->setGroundCover([
			VanillaBlocks::SAND(),
			VanillaBlocks::SAND(),
			VanillaBlocks::SAND(),
			VanillaBlocks::SAND(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
			VanillaBlocks::SANDSTONE(),
		]);
	}

	public function getName(): string
	{
		return "BetterDesert";
	}

	public function getId(): int
	{
		return Biome::DESERT;
	}
}