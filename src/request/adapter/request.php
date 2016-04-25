<?php
declare(strict_types=1);
namespace bluefin\http\request\adapter;
use bluefin\http\request\request as requestInterface;
class request implements requestInterface
{
	private $_pathinfo;
	private $_uri   = null;
	private $_query = null;

	public function __construct()
	{
		if(strpos($_SERVER['REQUEST_URI'], '?')!==false) {
			list($uri, $query) = explode('?', $_SERVER['REQUEST_URI']);
			parse_str($query, $this->_query);
		} else {
			$uri = $_SERVER['REQUEST_URI'];
		}

		$this->_pathinfo = pathinfo($uri);
		$this->_uri      = $uri;
	}

	public function getMethod():string
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') ? 'AJAX' : $_SERVER['REQUEST_METHOD'];
	}

	public function getUri():string
	{
		return $this->_uri;
	}

	public function getScheme():string
	{
		return isset($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : 'http';
	}

	public function getHost():string
	{
		return $_SERVER['HTTP_HOST'];
	}

	public function getPort():string
	{
		return $_SERVER['SERVER_PORT'];
	}

	public function getPath():string
	{
		return $this->_pathinfo['dirname'];
	}

	public function getBasename():string
	{
		return $this->_pathinfo['basename'];
	}

	public function getFilename():string
	{
		return $this->_pathinfo['filename'];
	}

	public function getExtension():string
	{
		return isset($this->_pathinfo['extension']) ? $this->_pathinfo['extension'] : null;
	}

	public function getQueryString():string
	{
		return $_SERVER['QUERY_STRING'];
	}

	public function getQuery(string $key):string
	{
		return isset($this->_query[$key]) ? $this->_query[$key] : '';
	}

	public function getClientIp():string
	{
		if(isset($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif(isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		} else{
			return null;
		}
	}

	public function getUser():string
	{
		return isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
	}

	public function getPassword():string
	{
		return isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
	}

	public function getReferer():string
	{
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
	}

	public function getUserAgent():string
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}

	public function getHeader(string $name):string
	{
	}

	public function __get(string $key):string
	{
		$method = 'get'.$key;
		return method_exists(__CLASS__, $method) ? $this->$method() : '';
	}
}
