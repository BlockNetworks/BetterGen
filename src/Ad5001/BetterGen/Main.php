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

namespace Ad5001\BetterGen;

use Ad5001\BetterGen\generator\BetterNormal;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\GeneratorManager;

class Main extends PluginBase
{

	/**
	 * Called when the plugin enales
	 *
	 * @return void
	 */
	public function onEnable(): void
	{
		GeneratorManager::getInstance()->addGenerator(BetterNormal::class, "betternormal");
	}

	/**
	 * Builds a structure randomly based on a circle algorithm. Used in caves and lakes.
	 *
	 * @param ChunkManager $world
	 * @param Vector3 $pos
	 * @param Vector3 $infos
	 * @param Random $random
	 * @param Block $block
	 * @return void
	 */
	public static function buildRandom(ChunkManager $world, Vector3 $pos, Vector3 $infos, Random $random, Block $block)
	{
		$doNotOverwrite = [
			BlockLegacyIds::WATER,
			BlockLegacyIds::STILL_WATER,
			BlockLegacyIds::STILL_LAVA,
			BlockLegacyIds::LAVA,
			BlockLegacyIds::BEDROCK,
			BlockLegacyIds::CACTUS,
			BlockLegacyIds::PLANKS
		];

		$xBounded = $random->nextBoundedInt(3) - 1;
		$yBounded = $random->nextBoundedInt(3) - 1;
		$zBounded = $random->nextBoundedInt(3) - 1;
		$pos = $pos->round();
		for ($x = $pos->x - ($infos->x / 2); $x <= $pos->x + ($infos->x / 2); $x++) {
			for ($y = $pos->y - ($infos->y / 2); $y <= $pos->y + ($infos->y / 2); $y++) {
				for ($z = $pos->z - ($infos->z / 2); $z <= $pos->z + ($infos->z / 2); $z++) {
					if (abs((abs($x) - abs($pos->x)) ** 2 + ($y - $pos->y) ** 2 + (abs($z) - abs($pos->z)) ** 2) < ((($infos->x / 2 - $xBounded) + ($infos->y / 2 - $yBounded) + ($infos->z / 2 - $zBounded)) / 3) ** 2 &&
						$y > 0 && !in_array($world->getBlockAt($x, $y, $z)->getId(), $doNotOverwrite) &&
						!in_array($world->getBlockAt($x, $y + 1, $z), $doNotOverwrite)
					) {
						$world->setBlockAt($x, $y, $z, $block);
					}
				}
			}
		}
	}
}
