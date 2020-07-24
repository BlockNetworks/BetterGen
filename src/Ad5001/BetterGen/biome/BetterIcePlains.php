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

use pocketmine\block\VanillaBlocks;
use pocketmine\world\biome\Biome;
use pocketmine\world\biome\SnowyBiome;
use pocketmine\world\generator\populator\TallGrass;

class BetterIcePlains extends SnowyBiome implements Mountainable
{

	public function __construct()
	{
		parent::__construct();
		$this->setGroundCover([
			VanillaBlocks::SNOW(),
			VanillaBlocks::GRASS(),
			VanillaBlocks::DIRT(),
			VanillaBlocks::DIRT(),
			VanillaBlocks::DIRT()
		]);

		$tallGrass = new TallGrass();
		$tallGrass->setBaseAmount(3);
		$this->addPopulator($tallGrass);

		$this->setElevation(63, 74);

		$this->temperature = 0.05;
		$this->rainfall = 0.8;
	}

	public function getName(): string
	{
		return "BetterIcePlains";
	}

	public function getId(): int
	{
		return Biome::ICE_PLAINS;
	}
}