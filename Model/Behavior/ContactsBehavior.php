<?php

class ContactsBehavior extends ModelBehavior 
{
	public $content = null; // content returned from the remote web server
	public $url = null;
	public $Curl = null;
	public $curlError = null;
	public $curlErrno = null;
	public $curlHeaders = null;
	public $curlRequestHeaders = null;
	public $curlCookieFile = null;
	public $curlCookieJar = null;
	public $gettingLive = false;
	
	public $searchFormUrl = 'https://example.com/search.aspx';
	
	public $ignoreHeaders = array(
		'cookie', 'addr', 'port', 'accept_encoding', 'host', 'accept_language',
		'accept_language', 'connection', 'accept', 'cache_control', 'referer'
	);
	
	public $DirPerson = false;
	
	public function setup(Model $Model, $settings = array())
	{
		$Model->curlError = $this->curlError;
		$Model->curlErrno = $this->curlErrno;
		$Model->curlHeaders = $this->curlHeaders;
		$Model->curlRequestHeaders = $this->curlRequestHeaders;
		$Model->gettingLive = $this->gettingLive;
		
		if(!$Model->Behaviors->loaded('Usage.Usage'))
		{
			$Model->Behaviors->load('Usage.Usage');
		}
		
		if(!$Model->Behaviors->enabled('Usage.Usage'))
		{
			$Model->Behaviors->enable('Usage.Usage');
		}
		
		if(!$Model->Behaviors->loaded('Utilities.DomParser'))
		{
			$Model->Behaviors->load('Utilities.DomParser');
		}
		
		if(!$Model->Behaviors->enabled('Utilities.DomParser'))
		{
			$Model->Behaviors->enable('Utilities.DomParser');
		}
		
		if(!$this->DirPerson and $Model->alias != 'DirPerson')
		{
			App::import('Contacts.Model', 'DirPerson');
			$this->DirPerson = new DirPerson();
		}
	}
	
	public function Contacts_getSiteMinderInfo(Model $Model, $includeDir = true)
	{
		if(!isset($_COOKIE['SMSESSION']) and !isset($_COOKIE['NIHSMSESSION']))
			return false;
		
		$userInfo = array();
		
		foreach($_SERVER as $headerKey => $headerValue)
		{
			$matches = array();
			if($headerKey == 'REMOTE_USER')
			{
				if(!isset($_SERVER['HTTP_USER_DN']))
					$headerKey = 'HTTP_USER_DN';
			}
			
			if(preg_match('/^(HTTP_|REMOTE_)(.*)/i', $headerKey, $matches))
			{
				if(!isset($matches[2]))
					continue;
				$headerKey = strtolower($matches[2]);
				
				if($headerKey == 'user_dn')
				{
					$headerParts = explode(',', $headerValue);
					// the first one is the 
					$adAccount = array_shift($headerParts);
					$adAccount = preg_replace('/^CN=/', '', $adAccount);
					$adAccount = trim($adAccount);
					$adAccount = strtolower($adAccount);
					$userInfo['adaccount'] = $adAccount;
					$userInfo['remote_user'] = $headerValue;
					continue;
				}
				elseif($headerKey == 'sm_user' or $headerKey == 'user_samaccountname')
				{
					if(!isset($userInfo['adaccount']))
						$userInfo['adaccount'] = $headerValue;
					continue;
				}
				elseif($headerKey == 'mail')
				{
					if(!isset($userInfo['email']))
						$userInfo['email'] = $headerValue;
					continue;
				}
				elseif($headerKey == 'hhsid' or $headerKey == 'user_employeeid')
				{
					if(!isset($userInfo['userid']))
						$userInfo['userid'] = $headerValue;
					continue;
				}
				elseif($headerKey == 'user_telephone')
				{
					if(!isset($userInfo['user_telephone']))
						$userInfo['phone'] = $headerValue;
					continue;
				}
				elseif(in_array($headerKey, $this->ignoreHeaders))
				{
					continue;
				}
				
				$userInfo[$headerKey] = $headerValue;
			}
		}
		
		if($includeDir and isset($userInfo['adaccount']))
		{
			if($userInfo = $this->Contacts_getInfoByUsername($Model, $userInfo['adaccount']))
			{
				if(!isset($userInfo['email']) and isset($userInfo['email'])) 
				{
					$userInfo['email'] = $userInfo['email'];
				}
				
				$userInfo = array_merge($userInfo, $userInfo);
			}
		}
		
		return ($userInfo?$userInfo:false);
	}
	
	public function Contacts_getInfoByUsername(Model $Model, $username = false)
	{
		$Model->modelError = false;
		if(!$username)
		{
			$Model->modelError = __('Unknown Username');
			return false;
		}
		
		if(!$info = $this->DirPerson->getUserInfo($username))
		{
			$Model->modelError = $this->DirPerson->modelError;
		}
		return $info;
	}
	
	public function Contacts_getInfoByEmail(Model $Model, $email = false)
	{
		$Model->modelError = false;
		if(!$email)
		{
			$Model->modelError = __('Unknown email');
			return false;
		}
		
		if(!$info = $this->DirPerson->getUserInfo(false, $email))
		{
			$Model->modelError = $this->DirPerson->modelError;
		}
		return $info;
	}
	
	public function Contacts_getInfoByuserid(Model $Model, $userid = false)
	{
		$Model->modelError = false;
		if(!$userid)
		{
			$Model->modelError = __('Unknown userid');
			return false;
		}
		
		if(!$info = $this->DirPerson->getUserInfo(false, false, $userid))
		{
			$Model->modelError = $this->DirPerson->modelError;
		}
		return $info;
	}
	
	public function Contacts_testDir(Model $Model, $username = false)
	{
		return $this->DirPerson->testConnection($username);
	}
	
	/* There is a similar copy of this in Contacts.ContactsHelper. If you update this, update that*/
	public function Contacts_makePath(Model $Model, $object = array(), $options = array())
	{
		$defaultOptions = array(
			'compiled' => true,
		);
		
		$options = array_merge($defaultOptions, $options);
		
		// support for Cerberus' Fisma Systems/Inventories
		if(isset($object['FismaSystem']['OwnerContact']))
		{
			$object['AdAccount'] = $object['FismaSystem']['OwnerContact'];
		}
		
		$originalObject = $object;
		$object = Hash::flatten($object);
		
		$parts = array();
		// support for Cerberus' Fisma Systems/Inventories
		if(isset($object['FismaInventory.id']))
		{
			if($object['FismaInventory.name'])
				$parts['FismaInventory'] = $object['FismaInventory.name'];
			elseif($object['FismaInventory.asset_tag'])
				$parts['FismaInventory'] = $object['FismaInventory.asset_tag'];
			elseif($object['FismaInventory.dns_name'])
				$parts['FismaInventory'] = $object['FismaInventory.dns_name'];
			elseif($object['FismaInventory.ip_address'])
				$parts['FismaInventory'] = $object['FismaInventory.ip_address'];
			elseif($object['FismaInventory.mac_address'])
				$parts['FismaInventory'] = $object['FismaInventory.mac_address'];
			
			$parts['FismaInventory'] = $parts['FismaInventory'];
			if(!$options['compiled'])
				$parts['FismaInventory'] = $originalObject['FismaInventory'];
		}
		// support for Cerberus' Fisma Systems/Inventories
		if(isset($object['FismaSystem.id']))
		{
			$parts['FismaSystem'] = $object['FismaSystem.name'];
			if(!$options['compiled'])
				$parts['FismaSystem'] = $originalObject['FismaSystem'];
		}
		if(isset($object['AssocAccount.id']))
		{
			$parts['AssocAccount'] = $object['AssocAccount.username'];
			if(!$options['compiled'])
				$parts['AssocAccount'] = $originalObject['AssocAccount'];
		}
		
		if(isset($object['AdAccount.id']))
		{
			$parts['AdAccount'] = $object['AdAccount.username'];
			if(!$options['compiled'])
				$parts['AdAccount'] = $originalObject['AdAccount'];
		}
		
		if(isset($object['AdAccount.Sac.id']))
		{
			$parts['Sac'] = $object['AdAccount.Sac.shortname'];
			if(!$options['compiled'])
				$parts['Sac'] = $originalObject['AdAccount']['Sac'];
		}
		elseif(isset($object['Sac.id']))
		{
			$parts['Sac'] = $object['Sac.shortname'];
			if(!$options['compiled'])
				$parts['Sac'] = $originalObject['Sac'];
		}
		
		if(isset($object['AdAccount.Sac.Branch.id']))
		{
			$parts['Branch'] = $object['AdAccount.Sac.Branch.shortname'];
			if(!$options['compiled'])
				$parts['Branch'] = $originalObject['AdAccount']['Sac']['Branch'];
		}
		elseif(isset($object['Sac.Branch.id']))
		{
			$parts['Branch'] = $object['Sac.Branch.shortname'];
			if(!$options['compiled'])
				$parts['Branch'] = $originalObject['Sac']['Branch'];
		}
		elseif(isset($object['Branch.id']))
		{
			$parts['Branch'] = $object['Branch.shortname'];
			if(!$options['compiled'])
				$parts['Branch'] = $originalObject['Branch'];
		}
		
		if(isset($object['AdAccount.Sac.Branch.Division.id']))
		{
			$parts['Division'] = $object['AdAccount.Sac.Branch.Division.shortname'];
			if(!$options['compiled'])
				$parts['Division'] = $originalObject['AdAccount']['Sac']['Branch']['Division'];
		}
		elseif(isset($object['Sac.Branch.Division.id']))
		{
			$parts['Division'] = $object['Sac.Branch.Division.shortname'];
			if(!$options['compiled'])
				$parts['Division'] = $originalObject['Sac']['Branch']['Division'];
		}
		elseif(isset($object['Branch.Division.id']))
		{
			$parts['Division'] = $object['Branch.Division.shortname'];
			if(!$options['compiled'])
				$parts['Division'] = $originalObject['Branch']['Division'];
		}
		elseif(isset($object['Division.id']))
		{
			$parts['Division'] = $object['Division.shortname'];
			if(!$options['compiled'])
				$parts['Division'] = $originalObject['Division'];
		}
		if(isset($object['AdAccount.Sac.Branch.Division.Org.id']))
		{
			$parts['Org'] = $object['AdAccount.Sac.Branch.Division.Org.shortname'];
			if(!$options['compiled'])
				$parts['Org'] = $originalObject['AdAccount']['Sac']['Branch']['Division']['Org'];
		}
		elseif(isset($object['Sac.Branch.Division.Org.id']))
		{
			$parts['Org'] = $object['Sac.Branch.Division.Org.shortname'];
			if(!$options['compiled'])
				$parts['Org'] = $originalObject['Sac']['Branch']['Division']['Org'];
		}
		elseif(isset($object['Branch.Division.Org.id']))
		{
			$parts['Org'] = $object['Branch.Division.Org.shortname'];
			if(!$options['compiled'])
				$parts['Org'] = $originalObject['Branch']['Division']['Org'];
		}
		elseif(isset($object['Division.Org.id']))
		{
			$parts['Org'] = $object['Division.Org.shortname'];
			if(!$options['compiled'])
				$parts['Org'] = $originalObject['Division']['Org'];
		}
		elseif(isset($object['Org.id']))
		{
			$parts['Org'] = $object['Org.shortname'];
			if(!$options['compiled'])
				$parts['Org'] = $originalObject['Org'];
		}
		
		$parts = array_reverse($parts);
		
		if(!$options['compiled'])
			return $parts;
		
		return implode('/', $parts);
	}
	
	public function Contacts_orgChart(Model $Model, $args = array())
	{
		$contain = array();
		if($Model->name == 'Division')
			$contain = array('Org');
		elseif($Model->name == 'Branch')
			$contain = array('Division', 'Division.Org');
		elseif($Model->name == 'Sac')
			$contain = array('Branch', 'Branch.Division', 'Branch.Division.Org');
		elseif($Model->name == 'AdAccount')
			$contain = array('Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org');
		elseif($Model->name == 'FismaSystem')
			$contain = array('OwnerContact', 'OwnerContact.Sac', 'OwnerContact.Sac.Branch', 'OwnerContact.Sac.Branch.Division', 'OwnerContact.Sac.Branch.Division.Org');
		
		if(!isset($args['contain']))
			$args['contain'] = array();
		$args['contain'] = array_merge($contain, $args['contain']);
		
		$findType = 'all';
		if(isset($args['findType']))
		{
			$findType = $args['findType'];
			unset($args['findType']);
		}
		
		$results = $Model->find($findType, $args);
		
		$out = array(
			__('NIH') => array(),
		);
		
		foreach($results as $i => $result)
		{
			$result = $this->Contacts_makePath($Model, $result, array('compiled' => false));
			
			$org = $division = $branch = $sac = $adaccount = $fismasystem = 'Unknown';
			
			if(isset($result['Org']))
			{
				$org = json_encode(array('name' => $result['Org']['shortname'], 'title' => $result['Org']['name'], 'url' => Router::url(array('controller' => 'orgs', 'action' => 'view', $result['Org']['id']))));
				if(!isset($out[__('NIH')][$org]))
					$out[__('NIH')][$org] = $org;
			}
			if(isset($result['Division']))
			{
				$division = json_encode(array('name' => $result['Division']['shortname'], 'title' => $result['Division']['name'], 'url' => Router::url(array('controller' => 'divisions', 'action' => 'view', $result['Division']['id']))));
				if(!isset($out[__('NIH')][$org]) or !is_array($out[__('NIH')][$org]))
					$out[__('NIH')][$org] = array();
				if(!isset($out[__('NIH')][$org][$division])) 
					$out[__('NIH')][$org][$division] = $division;
			}
			if(isset($result['Branch']))
			{
				$branch = json_encode(array('name' => $result['Branch']['shortname'], 'title' => $result['Branch']['name'], 'url' => Router::url(array('controller' => 'branches', 'action' => 'view', $result['Branch']['id']))));
				if(!isset($out[__('NIH')][$org][$division]) or !is_array($out[__('NIH')][$org][$division]))
					$out[__('NIH')][$org][$division] = array();
				if(!isset($out[__('NIH')][$org][$division][$branch])) 
					$out[__('NIH')][$org][$division][$branch] = $branch;
			}
			if(isset($result['Sac']))
			{
				$sac = json_encode(array('name' => $result['Sac']['shortname'], 'title' => $result['Sac']['name'], 'url' => Router::url(array('controller' => 'sacs', 'action' => 'view', $result['Sac']['id']))));
				if(!isset($out[__('NIH')][$org][$division][$branch]) or !is_array($out[__('NIH')][$org][$division][$branch]))
					$out[__('NIH')][$org][$division][$branch] = array();
				if(!isset($out[__('NIH')][$org][$division][$branch][$sac])) 
					$out[__('NIH')][$org][$division][$branch][$sac] = $sac;
			}
			if(isset($result['AdAccount']))
			{
				$adaccount = json_encode(array('name' => $result['AdAccount']['username'], 'title' => $result['AdAccount']['name'], 'url' => Router::url(array('controller' => 'ad_accounts', 'action' => 'view', $result['AdAccount']['id']))));
				if(!isset($out[__('NIH')][$org][$division][$branch][$sac]) or !is_array($out[__('NIH')][$org][$division][$branch][$sac]))
					$out[__('NIH')][$org][$division][$branch][$sac] = array();
				if(!isset($out[__('NIH')][$org][$division][$branch][$sac][$adaccount])) 
					$out[__('NIH')][$org][$division][$branch][$sac][$adaccount] = $adaccount;
			}
			if(isset($result['FismaSystem']))
			{
				$class = '';
				if(!$result['FismaSystem']['parent_id'])
					$class = 'parent';
				$fismasystem = json_encode(array('name' => $result['FismaSystem']['name'], 'title' => $result['FismaSystem']['fullname'], 'class' => $class, 'url' => Router::url(array('controller' => 'fisma_systems', 'action' => 'view', $result['FismaSystem']['id']))));
				if(!isset($out[__('NIH')][$org][$division][$branch][$sac][$adaccount]) or !is_array($out[__('NIH')][$org][$division][$branch][$sac][$adaccount]))
					$out[__('NIH')][$org][$division][$branch][$sac][$adaccount] = array();
				if(!isset($out[__('NIH')][$org][$division][$branch][$sac][$adaccount][$fismasystem])) 
					$out[__('NIH')][$org][$division][$branch][$sac][$adaccount][$fismasystem] = $fismasystem;
			}
		}
		
		return $out;
	}
	
/*
	below will be marked as deprecated in the future
*/
	
	public function Contacts_getInfoById(Model $Model, $userid = false)
	{
		$html = $this->Contacts_getRemote($Model, 'https://example.com/details?id='. $userid);
		$html = $Model->DP_parse($html);

		if(!$rows = $html->find('table#ctl00_ContentPlaceHolder_dvPerson tr'))
		{
			$rows = $html->find('table#ContentPlaceHolder_dvPerson tr');
		}
		
		$userInfo = array();
		
		foreach($rows as $row)
		{
			// the header
			$headerKey = false;
			$headerValue = false;
			foreach($row->find('td') as $cell)
			{
				if(trim($cell->getAttribute('class')) == 'DVHeader')
				{
					$headerKey = trim($cell->innertext);
					$headerKey = strtolower($headerKey);
					$headerKey = preg_replace('/:$/', '', $headerKey);
					$headerKey = preg_replace('/\s+/', '_', $headerKey);
					
					// clean up the header key specifically
					if($headerKey == 'hhs_id')
						$headerKey = 'hhsid';
				}
				else
				{
					$headerValue = trim($cell->innertext);
					$headerValue = strip_tags($headerValue, '<br>');
					$headerValue = preg_replace('/<br\s*\/>/i', '~~~~', $headerValue);
					$headerValue = strip_tags($headerValue, '<br>');
					$headerValue = str_replace('~~~~', "\n", $headerValue);
					if($headerValue == '&nbsp;')
						$headerValue = false;
					
					if($headerKey == 'hhsid')
						$headerValue = str_replace('-', '', $headerValue);
					
					$headerValue = preg_replace('/\h+/i', ' ', $headerValue);
				}
			}
			
			if($headerKey)
			{
				$userInfo[$headerKey] = $headerValue;
			}
		}
		
		return $userInfo;
	}
	
	public function getSearchForm(Model $Model, $username = false)
	{
		return false;
	}
	
	public function Contacts_getRemote(Model $Model, $url = null, $query = array(), $method = 'get', $headers = array(), $curl_options = array())
	{
		$this->gettingLive = $Model->gettingLive = false;
		$data = false;
		$cacheKey = md5(serialize(array('url' => $url, 'query' => $query, 'method' => $method)));
		
		$readCache = true;
		
		if(Cache::read('debug') > 1)
		{
			$readCache = false;
		}
		
		if(isset($curl_options['checkCache']))
		{
			if($curl_options['checkCache'] === false)
			{
				$readCache = false;
			}
			unset($curl_options['checkCache']);
		}
		
		if($readCache)
		{
			$data = Cache::read($cacheKey, 'ad_account_info');
		}
		
		if ($data !== false)
		{
			$Model->Usage_updateCounts('ad_account_info', 'cached');
			$Model->Usage_updateCounts('cached', 'ad_account_info');
			
			$Model->shellOut(__('Loaded from cache with key: %s', $cacheKey), 'ad_account_info', 'info');
			return $data;
		}
		
		$curl_options_default = array(
			'followLocation' => true,
			'maxRedirs' => 5,
			'timeout' => 20,
			'connectTimeout' => 20,
			'cookieFile' => CACHE. 'dt_cookieFile_'. getmypid(),
			'cookieJar' => CACHE. 'dt_cookieJar_'. getmypid(),
			'header' => true,
			'headerOut' => true,
		);
		$curl_options = array_merge($curl_options_default, $curl_options);
		
		if($url)
		{
			if(!$this->Curl)
			{
				// load the curl object
				$Model->shellOut(__('Loading cUrl.'), 'ad_account_info', 'info');
				App::import('Vendor', 'Utilities.Curl');
				$this->Curl = new Curl();
			}
			
			$this->Curl->referer = $url;
			
			foreach($curl_options as $k => $v)
			{
				$this->Curl->{$k} = $v;
			}
			
			$query_url = '';
			if(is_array($query) and !empty($query))
			{
				$query_url = array();
				foreach($query as $k => $v)
				{
					$query_url[] = $k. '='. $v;
				}
				$query_url = '?'. implode('&', $query_url);
			}
			
			if(is_array($headers) and !empty($headers))
			{
				$this->Curl->httpHeader = $headers;
			}
			
			if($method == 'post')
			{
				$this->Curl->post = true;
				$this->Curl->postFieldsArray = $query;
				$Model->shellOut(__('POST URL: %s - POST Query: %s', $url, $query_url), 'ad_account_info', 'info');
				$this->Curl->url = $url;
			}
			else
			{
				$this->Curl->post = false;
				$url .= $query_url;
				$Model->shellOut(__('GET URL: %s', $url), 'ad_account_info', 'info');
				$this->Curl->url = $url;
			}
			
			// going for a live connection
			$this->gettingLive = $Model->gettingLive = true;
			
			$Model->Usage_updateCounts('ad_account_info', 'remote');
			$Model->Usage_updateCounts('remote', 'ad_account_info');
			
			$data = $this->Curl->execute();
			
			$this->curlInfo = $this->Curl->getInfo();
			
			if($this->Curl->error)
			{
				$Model->Usage_updateCounts('ad_account_info', 'remote_error');
				$Model->Usage_updateCounts('remote_error', 'ad_account_info');
				
				$Model->curlError = $this->curlError = $this->Curl->error;
				$Model->curlErrno = $this->curlErrno = $this->Curl->errno;
				
				$logtype = 'error';
				if($this->curlErrno == 28)
				{
					$Model->Usage_updateCounts('remote_error_timeout', 'ad_account_info');
				}
				
					
				$Model->shellOut(__('Curl Error: (%s) %s -- Url: %s', $this->curlErrno, $this->curlError, $url), 'ad_account_info', $logtype);
			}
			else
			{
				if($this->Curl->response_headers)
				{
					$Model->curlHeaders = $this->curlHeaders = $this->Curl->response_headers;
				}
				if($this->Curl->request_headers)
				{
					$Model->curlRequestHeaders = $this->curlRequestHeaders = $this->Curl->request_headers;
				}
				
				// cache it
				Cache::write($cacheKey, $data, 'ad_account_info');
				$Model->Usage_updateCounts('ad_account_info', 'remote_success');
				$Model->Usage_updateCounts('remote_success', 'ad_account_info');
			}
			
			$this->Curl->close();
			$this->Curl = false;
			if($curl_options['cookieJar'])
			{
				$this->curlCookieJar = $this->parseCookieJar($Model, $curl_options['cookieJar']);
				if(is_readable($curl_options['cookieJar'])) unlink($curl_options['cookieJar']);
			}
			if($curl_options['cookieFile'])
			{
				$this->curlCookieFile = $this->parseCookieJar($Model, $curl_options['cookieFile']);
				if(is_readable($curl_options['cookieFile'])) unlink($curl_options['cookieFile']);
			}
		}
		return $data;
	}
	
	protected function parseCookieJar(Model $Model, $cookieJarPath = false)
	{
		$out = array();
		if(!is_readable($cookieJarPath)) 
		{
			return $out;
		}
		
		if(!$lines = file($cookieJarPath))
		{
			return $out;
		}
		
		foreach($lines as $line) 
		{
			$line = trim($line);
			if(!$line) continue;
			if($line[0] == '#') continue;
			
			if(substr_count($line, "\t") !== 6) continue;
			
			$tokens = explode("\t", $line);
			$tokens = array_map('trim', $tokens);
			
			$out[] = array(
				'domain' => $tokens[0],
				'flag' => (strtoupper($tokens[1]) === 'TRUE'?true:false),
				'path' => $tokens[2],
				'secure' => (strtoupper($tokens[3]) === 'TRUE'?true:false),
				'expiration' => date('Y-m-d H:i:s', strtotime($tokens[4])),
				'name' => $tokens[5],
				'value' => $tokens[6],
			);
		}
		return $out;
	}
}