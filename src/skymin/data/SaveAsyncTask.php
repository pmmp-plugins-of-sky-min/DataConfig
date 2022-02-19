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
use pocketmine\scheduler\AsyncTask;

use PrefixedLogger;

use function mkdir;
use function is_dir;
use function dirname;
use function strlen;
use function rename;
use function unlink;
use function yaml_emit;
use function json_encode;
use function is_string;
use function array_keys;
use function file_exists;
use function file_put_contents;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;
use const YAML_UTF8_ENCODING;

final class SaveAsyncTask extends AsyncTask{

	private PrefixedLogger $logger;

	public function __construct(
		private string $fileName,
		private int $type,
		private array $data
	){
		$this->logger = new PrefixedLogger(Server::getInstance()->getLogger(), 'DataConfig');
	}

	public function onRun() :void{
		$fileName = $this->fileName;
		$data = (array) $this->data;
		if(is_dir($fileName)){
			$this->setResult(false);
			return;
		}
		$dir = dirname($fileName);
		if(!is_dir($dir)){
			mkdir($dier);
		}
		$count = 0;
		do{
			$tmpFileName = $fileName . ".$count.tmp";
			$count++;
		}while(is_dir($tmpFileName) || file_exists($tmpFileName));
		$content = match($this->type){
			Data::YAML => yaml_emit($data, YAML_UTF8_ENCODING),
			Data::JSON => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
			Data::LIST => implode("\n", array_keys($data)),
			default => false
		};
		if(!is_string($content)){
			unlink($tmpFileName);
			$this->setResult(false);
			return;
		}
		$result = file_put_contents($tmpFileName, $content);
		if($result !== strlen($content)){
			unlink($tmpFileName);
			$this->setResult(false);
			return;
		}
		rename($tmpFileName, $fileName);
		$this->setResult(true);
	}

	public function onCompletion() :void{
		if(!$this->getResult()){
			$this->logger->error('Failed to save Data at' . $this->fileName);
		}
	}

}
