<?php
namespace bluefin\http\client;
interface client
{
	public function setHeader(string $name, string $value):bool;
	public function setHeaders(array $headers):bool;
	public function head(string $url):string;
	public function get(string $url):string;
	public function post(string $url, array $body):string;
	public function put(string $url, array $body):string;
	public function delete(string $url):string;
	public function trace(string $url):string;
}
