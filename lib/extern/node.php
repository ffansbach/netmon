<?php
/**
 * a single node of a network
 */
class node
{
	/**
	 * node id
	 * @var int|string|null
	 */
	private $_id = null;

	/**
	 * readable name
	 *
	 * @var string|null
	 */
	private $_name = null;

	/**
	 * nodetype
	 *
	 * AccesPoint, Gateway, Switch
	 *
	 * @var string
	 */
	private $_type = 'AccessPoint';

	/**
	 * url for details about that node
	 *
	 * @var stringnull
	 */
	private $_href = null;

	/**
	 * status of the node
	 *
	 * will contain state (online, offline),
	 * clients, lastcontact (ts)
	 *
	 * @var mixed|null
	 */
	private $_status = null;

	/**
	 * owner of that node
	 *
	 * will contain id, name, href
	 *
	 * @var mixed|null
	 */
	private $_user = null;

	/**
	 * geolocation of that node
	 *
	 * lat, long
	 * @var array|null
	 */
	private $_geo = null;

	/**
	 * create a new node with id and name
	 *
	 * @param int|string $id
	 * @param string $name
	 */
	public function __construct($id, $name)
	{
		$this->setId($id);
		$this->setName($name);
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->_id = $id;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->_type = $type;
	}

	/**
	 * @param string $href
	 */
	public function setHref($href)
	{
		$this->_href = $href;
	}

	/**
	 * set the status
	 *
	 * @param string $status
	 * @param int $clients
	 * @param int $lastcontact
	 */
	public function setStatus($status, $clients = false, $lastcontact = false)
	{
		$this->_status = array(
			'online' => (boolean)$status
		);

		if($clients !== false)
		{
			$this->_status['clients'] = (int)$clients;
		}

		if($lastcontact != false)
		{
			$this->_status['lastcontact'] = date('c', (int)$lastcontact);
		}
	}

	/**
	 * @param int|string $id
	 * @param string|boolean $name
	 * @param string|boolean $href
	 */
	public function setUserId($id)
	{
		$this->_user = $id;
	}

	/**
	 * set geolocation of that node
	 *
	 * @param string $lat
	 * @param string $long
	 */
	public function setGeo($lat, $long)
	{
		$this->_geo = array('lat' => $lat, 'long' => $long);
	}

	/**
	 * returns the node-object
	 *
	 * @return mixed
	 */
	public function getNode()
	{
		if(empty($this->_id))
		{
			throw new Exception('NodeID missing');
			return false;
		}
		if(empty($this->_name))
		{
			throw new Exception('Nodename missing');
			return false;
		}

		$node = array(
			'id' => $this->_id,
			'name' => $this->_name
		);

		if(!empty($this->_type))
		{
			$node['node_type'] = $this->_type;
		}

		if(!empty($this->_href))
		{
			$node['href'] = $this->_href;
		}

		if(!empty($this->_status))
		{
			$node['status'] = $this->_status;
		}

		if(!empty($this->_user))
		{
			$node['links'] = array(
				'admin-c' => array(
					'type'	=> 'people',
					'id'	=> $this->_user
				)
			);
		}

		if(!empty($this->_geo))
		{
			$node['location'] = $this->_geo;
		}

		return $node;
	}
}