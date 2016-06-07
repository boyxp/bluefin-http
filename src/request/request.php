<?php
declare(strict_types=1);
namespace bluefin\http\request;
interface request
{
	public function getMethod():string;
	public function getUri():string;
	public function getScheme():string;
	public function getHost():string;
	public function getPort():string;
	public function getPath():string;
	public function getBasename():string;
	public function getFilename():string;
	public function getExtension():string;
	public function getQueryString():string;
	public function getQuery(string $key):string;
	public function getClientIp():string;
	public function getUser():string;
	public function getPassword():string;
	public function getReferer():string;
	public function getUserAgent():string;
	public function getHeader(string $name):string;
	public function __get(string $key):string;
}
