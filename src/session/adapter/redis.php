<?php
declare(strict_types=1);
namespace bluefin\http\session\adapter;
use bluefin\orm\connection\adapter\redis as connection;
use bluefin\http\session\session as sessionInterface;
class redis implements sessionInterface
{
	private $_life   = 1200;
	private $_path   = '/';
	private $_domain = '';
	private $_secure = false;
	private $_http   = true;
	private $_redis  = null;
	private $_prefix = 'SESSION:';
	private $_cache  = null;

	public function __construct(connection $redis)
	{
		$this->_redis  = $redis;
		$this->_secure = (isset($_SERVER['HTTPS']) and isset($_SERVER['HTTP_X_FORWARDED_PORT']) and $_SERVER['HTTP_X_FORWARDED_PORT']==='443');
		$this->_domain = $_SERVER['SERVER_NAME'];
		$this->_prefix = "SESSION:{$_SERVER['SERVER_NAME']}:";
		session_set_save_handler($this, true);
	}

	public function start():bool
	{
		session_name('SECUREID');
		session_set_cookie_params(0, $this->_path, $this->_domain, $this->_secure, $this->_http);
		return session_start();
	}

	public function open($path=null, $name=null)
	{
		return true;
	}

	public function read($session_id)
	{
		$this->_cache = $this->_redis->get($this->_prefix.$session_id);
		$this->_redis->expire($this->_prefix.$session_id, $this->_life);
		return $this->_cache===null ? '' : $this->_cache;
	}

	public function write($session_id, $session_data)
	{
		if($session_data and $this->_cache!==$session_data) {
			$this->_redis->set($this->_prefix.$session_id, $session_data);
			$this->_cache = $session_data;
		}

		return true;
	}

	public function destroy($session_id)
	{
		$this->_cache = null;
		$this->_redis->del($this->_prefix.$session_id);
		return true;
	}

	public function gc($max_life_time)
	{
		return true;
	}

	public function close()
	{
		return true;
	}


	public function setLifeTime(int $lifeTime):sessionInterface
	{
		$lifeTime = intval($lifeTime);
		if($lifeTime > 0) {
			$this->_life = $lifeTime;
		}

		return $this;
	}

	public function setPath(string $path):sessionInterface
	{
		$this->_path = $path;
		return $this;
	}

	public function setDomain(string $domain):sessionInterface
	{
		if(strpos($_SERVER['HTTP_HOST'], $domain)!==false) {
			$this->_domain = $domain;
			$this->_prefix = "SESSION:{$domain}:";
		}
		return $this;
	}

	public function setSecure(bool $secure):sessionInterface
	{
		$this->_secure = ($secure and isset($_SERVER['HTTPS']));
		return $this;
	}

	public function setHttpOnly(bool $httpOnly):sessionInterface
	{
		$this->_http = $httpOnly ? true : false;
		return $this;
	}

	public function __set(string $option, $value)
	{
		$method = 'set'.$option;
		if(method_exists(__CLASS__, $method)) {
			call_user_func(array($this, $method), $value);
		}
	}

	public function __get(string $option)
	{
	}
}
