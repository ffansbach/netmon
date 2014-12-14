<?php
/**
 * a list of networknodes
 */
class nodeList
{
	/**
	 * version for json
	 *
	 * @var string
	 */
	private $_version = '1.0.0';

	/**
	 * Human readable name for this community
	 *
	 * @var string
	 */
	private $_communityName = '';

	/**
	 * website/homepage of the community
	 *
	 * @var string
	 */
	private $_website = '';

	/**
	 * the url of the community-file used for freifunk-api
	 *
	 * @var string
	 */
	private $_communityFile = '';

	/**
	 * will hold all nodes
	 *
	 * @var array
	 */
	private $_nodeList = array();

	/**
	 * will contain all the admin-c
	 *
	 * @var array
	 */
	private $_peopleList = array();

	/**
	 * @param string
	 */
	public function setCommunityName($name)
	{
		if(!empty($name))
		{
			$this->_communityName = $name;
		}
	}

	/**
	 * @param string
	 */
	public function setWebsite($website)
	{
		if(!empty($website))
		{
			$this->_website = $website;
		}
	}

	/**
	 * @param string
	 */
	public function setCommunityFile($url)
	{
		if(!empty($url))
		{
			$this->_communityFile = $url;
		}
	}

	/**
	 * adds a single node to the list
	 *
	 * @param mixed[]
	 */
	public function addNode($node)
	{
		array_push($this->_nodeList, $node);
	}

	public function addPerson($id, $name, $href)
	{
		$this->_peopleList[$id] = array(
			'id'	=> $id,
			'name'	=> $name,
			'href'	=> $href
		);
	}

	/**
	 * returns the data containing all nodes
	 *
	 * @return mixed[]
	 */
	public function getList()
	{
		if(empty($this->_communityName))
		{
			throw new Exception('CommunityName missing');
		}

		if(empty($this->_website))
		{
			throw new Exception('Website missing');
		}

		if(empty($this->_nodeList))
		{
			throw new Exception('no Routers');
		}

		$list = array(
			'version' => $this->_version,
			'updated_at' => date("c"),
			'community' => array(
				'name' => $this->_communityName
			),
		);

		if(!empty($this->_communityFile))
		{
			$list['community']['href'] = $this->_communityFile;
		}

		$list['nodes '] = $this->_nodeList;

		foreach($this->_peopleList as $person)
		{
			$list['linked '][] = $person;
		}

		return $list;
	}
}
