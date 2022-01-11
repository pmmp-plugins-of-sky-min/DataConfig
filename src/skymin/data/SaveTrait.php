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

use function dirname;
use function is_dir;
use function mkdir;
use function rename;
use function file_put_contents;
use function unlink;
use function json_encode;
use function yaml_emit;
use function strlen;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;
use const YAML_UTF8_ENCODING;

trait SaveTrait{
	
	private static function restore(int $type, string $fileName, array $data) :bool{
		if(is_dir($fileName)){
			throw new \LogicException('Target file path already exists and is not a file');
		}
		$dir = dirname($fileName);
		if(!is_dir($dir)){
			mkdir($dir);
		}
		$count = 0;
		do{
			$tmpFileName = $fileName . ".$count.tmp";
			$count++;
		}while(is_dir($tmpFileName));
		if($type === 0){
			$content = yaml_emit($data, YAML_UTF8_ENCODING);
		}elseif($type === 1){
			$content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}else{
			return false;
		}
		$result = file_put_contents($tmpFileName, $content);
		if($result !== strlen($content)){
			unlink($tmpFileName);
			return false;
		}
		rename($tmpFileName, $fileName);
		return true;
	}
	
}