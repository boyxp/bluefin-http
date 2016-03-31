<?php
namespace bluefin\http\session;
interface session extends \SessionHandlerInterface
{
	public function start():bool;
	public function setLifeTime(int $lifeTime):session;
	public function setPath(string $path):session;
	public function setDomain(string $domain):session;
	public function setSecure(bool $secure):session;
	public function setHttpOnly(bool $httpOnly):session;
	public function __set(string $option, $value);
	public function __get(string $option);
}
