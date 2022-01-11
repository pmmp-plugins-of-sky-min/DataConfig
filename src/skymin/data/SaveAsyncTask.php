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

final class SaveAsyncTask extends AsyncTask{
	use SaveTrait;
	
	private PrefixedLogger $logger;
	
	public function __construct(
		private string $fileName,
		private array $data,
		private int $type
	){
		$this->logger = new PrefixedLogger(Server::getInstance()->getLogger(), 'DataConfig');
	}
	
	public function onRun() :void{
		$type = $this->type;
		$fileName = $this->fileName;
		$data = (array) $this->data;
		$this->logger->debug('Starting save data at ' .  $fileName);
		if(self::restore($type, $fileName, $data)){
			$this->setResult('Completed');
		}else{
			$this->setResult('Failed');
		}
	}
	
	public function onCompletion() :void{
		$this->logger->debug($this->getResult() . ' to save Data at' . $this->fileName);
	}
	
}