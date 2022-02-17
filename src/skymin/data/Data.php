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

use \RuntimeException;

use function trim;
use function explode;
use function yaml_parse;
use function json_decode;
use function str_replace;
use function preg_replace;
use function file_exists;
use function file_get_contents;

final class Data{

	public const YAML = 0; //.yml, .yaml
	public const JSON = 1; //.js, .json
	public const LIST = 2; //.txt

	/**
	 * @var mixed[]
	 * @phpstan-var array<string, mixed>
	 */ 
	public array $data;

	/**
	 * @param mixed[] $default
	 * @phpstan-param array<string, mixed> $default
	 */
	public function __construct(
	    private string $fileName,
	    private int $type = self::JSON,
	    array $default = []
	){
	    $this->load($default);
	}

	/**
	 * @param mixed[] $default
	 * @phpstan-param array<string, mixed> $default
	 */
	private function load(array $default) : void{
	    $fileName = $this->fileName;
	    if(!file_exists($fileName)){
	        $this->save();
	        return $default;
	    }
	    $content = file_get_contents($fileName);
	    if($content === false){
	        throw new RuntimeException('Unable to load file');
	    }
	    $result = match($this->type){
	        self::YAML => self::parseYaml($content),
	        self::JSON => json_decode($content, true),
	        self::LIST => self::parseList($content),
	        default => throw new RuntimeException('unknown data type')
	    };
	    if(!is_array($result)){
	        throw new RuntimeException('Failed to load' . $fileName);
	    }
	    $this->data = $result;
	}

	public function save() : void{
	    Server::getInstance()->getAsyncPool()->submitTask(new SaveAsyncTask($this->fileName, $this->type, $this->data));
	}

	public function getPath() : string{
	    return $this->fileName;
	}

	public function __get(?string $key) : mixed{
	    if($key === null){
	        return $this->data;
	    }
	    return $this->data[$key];
	}

	public function __set(?string $key, mixed $value) : void{
	    if($key === null){
	        $this->data = $value;
	        return;
	    }
	    $this->data[$key] = $value;
	}

	public function __isset(string $key) : bool{
	    return (isset($this->data[$key]));
	}

	public function __unset(string $key) : void{
	    unset($this->data[$key]);
	}

	private static function parseYaml(string $content) : mixed{
	    return yaml_parse(preg_replace("#^( *)(y|Y|yes|Yes|YES|n|N|no|No|NO|true|True|TRUE|false|False|FALSE|on|On|ON|off|Off|OFF)( *)\:#m", "$1\"$2\"$3:", $content));
	}

	private static function parseList(string $content) : array{
	    $result = [];
	    foreach(explode("\n", trim(str_replace("\r\n", "\n", $content))) as $str){
	        $str = trim($str);
	        if(trim($str) === '') continue;
	    	$result[] = $str;
	    }
	    return $result;
	}

}
