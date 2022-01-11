<?php
/**
 *      _                    _       
 *  ___| | ___   _ _ __ ___ (_)_ __  
 * / __| |/ / | | | '_ ` _ \| | '_ \ 
 * \__ \   <| |_| | | | | | | | | | |
 * |___/_|\_\\__, |_| |_| |_|_|_| |_|
 *           |___/ 
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 * 
 * @author skymin
 * @link   https://github.com/sky-min
 * @license https://opensource.org/licenses/MIT MIT License
 * 
 *   /\___/\
 * 　(∩`・ω・)
 * ＿/_ミつ/￣￣￣/
 * 　　＼/＿＿＿/
 *
 */

declare(strict_types = 1);

namespace skymin\data;

use pocketmine\Server;

use function file_exists;
use function file_get_contents;
use function json_decode;
use function yaml_parse;
use function preg_replace;

final class Data{
	use SaveTrait;
	
	public const YAML = 0;
	public const JSON = 1;
	
	public static function call(string $fileName, int $type = self::YAML, array $default = []) :array{
		if(!file_exists($fileName)){
			return $default;
		}
		$content = file_get_contents($fileName);
		if($type === self::YAML){
			return yaml_parse(preg_replace("#^( *)(y|Y|yes|Yes|YES|n|N|no|No|NO|true|True|TRUE|false|False|FALSE|on|On|ON|off|Off|OFF)( *)\:#m", "$1\"$2\"$3:", $content));
		}
		if($type === self::JSON){
			return json_decode($content, true);
		}else{
			throw new \LogicException('unknown type');
		}
	}
	
	public static function save(string $fileName, array $data, int $type = self::YAML, bool $async = true) :void{
		if($async){
			Server::getInstance()->getAsyncPool()->submitTask(new SaveAsyncTask($fileName, $data, $type));
			return;
		}
		self::restore($type, $fileName, $data);
	}
	
}