<?

class Kestrel
{
	protected
		$connected = false,
		$servers = array(),
		
		$kestrel = null;
		
	public function __construct($host, $port = 22133)
	{
		$this->addServer($host, $port);
	}
	
	public function addServer($host, $port = 22133)
	{
		$this->servers []= array(
			'host' => $host,
			'port' => $port,
		);
		
		return $this;
	}
	
	public function init()
	{
		$this->kestrel = new memcache;
		foreach ($this->servers as $server)
		{
			$this->kestrel->addServer($server['host'], $server['port']);
		}
		
		$this->connected = true;
	}
	
	public function get($key, $reliable = false)
	{
		if (! $this->connected)
		{
			$this->init();
		}
		
		if ($reliable)
		{
			$key .= '/open';
		}
		
		return $this->kestrel->get($key);
	}
	
	public function close($key)
	{
		if (! $this->connected)
		{
			$this->init();
		}
		
		return $this->kestrel->get($key . '/close');
	}
	
	public function abort($key)
	{
		if (! $this->connected)
		{
			$this->init();;
		}
		
		return $this->kestrel->get($key . '/abort');
	}
	
	public function getNext($key) //always a reliable read. does a close and a get
	{
		if (! $this->connected)
		{
			$this->init();
		}
		
		return $this->kestrel->get($key . '/close/open');
	}
	
	public function peek($key)
	{
		if (! $this->connected)
		{
			$this->init();
		}
		
		return $this->kestrel->get($key . '/peek');
	}
	
	public function set($key, $value, $expire = 0)
	{
		if (! $this->connected)
		{
			$this->init();
		}
		
		return $this->kestrel->set($key, $value, null, $expire);
	}
}