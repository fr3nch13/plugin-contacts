<?php

App::uses('Hash', 'Core');
App::uses('ContactsAppHelper', 'Contacts.View/Helper');

class ContactsHelper extends ContactsAppHelper 
{
	/* There is a similar copy of this in Contacts.ContactsBehavior. If you update this, update that*/
	public function makePath($object = array(), $asText = false)
	{
		// support for Cerberus' Fisma Systems/Inventories
		if(isset($object['FismaSystem']['OwnerContact']) and !isset($object['AdAccount']))
		{
			$object['AdAccount'] = $object['FismaSystem']['OwnerContact'];
		}
		elseif(isset($object['OwnerContact']) and !isset($object['AdAccount']))
		{
			$object['AdAccount'] = $object['OwnerContact'];
		}
		
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
			if(!$asText)
				$parts['FismaInventory'] = $this->Html->link($parts['FismaInventory'], array('controller' => 'fisma_inventories', 'action' => 'view', $object['FismaInventory.id'], 'prefix' => false));
		}
		// support for Cerberus' Fisma Systems/Inventories
		if(isset($object['FismaSystem.id']))
		{
			$parts['FismaSystem'] = $object['FismaSystem.name'];
			if(!$asText)
				$parts['FismaSystem'] = $this->Html->link($parts['FismaSystem'], array('controller' => 'fisma_systems', 'action' => 'view', $object['FismaSystem.id'], 'prefix' => false));
		}
		if(isset($object['AssocAccount.id']))
		{
			$parts['AssocAccount'] = $object['AssocAccount.username'];
			if(!$asText)
				$parts['AssocAccount'] = $this->Html->link($parts['AssocAccount'], array('controller' => 'assoc_accounts', 'action' => 'view', $object['AssocAccount.id'], 'prefix' => false));
		}
		
		if(isset($object['AdAccount.id']))
		{
			$parts['AdAccount'] = $object['AdAccount.username'];
			if(!$asText)
				$parts['AdAccount'] = $this->Html->link($parts['AdAccount'], array('controller' => 'ad_accounts', 'action' => 'view', $object['AdAccount.id'], 'prefix' => false));
		}
		
		if(isset($object['AdAccount.Sac.id']))
		{
			$parts['Sac'] = $object['AdAccount.Sac.shortname'];
			if(!$asText)
				$parts['Sac'] = $this->Html->link($parts['Sac'], array('controller' => 'sacs', 'action' => 'view', $object['AdAccount.Sac.id'], 'prefix' => false));
		}
		elseif(isset($object['Sac.id']))
		{
			$parts['Sac'] = $object['Sac.shortname'];
			if(!$asText)
				$parts['Sac'] = $this->Html->link($parts['Sac'], array('controller' => 'sacs', 'action' => 'view', $object['Sac.id'], 'prefix' => false));
		}
		
		if(isset($object['AdAccount.Sac.Branch.id']))
		{
			$parts['Branch'] = $object['AdAccount.Sac.Branch.shortname'];
			if(!$asText)
				$parts['Branch'] = $this->Html->link($parts['Branch'], array('controller' => 'branches', 'action' => 'view', $object['AdAccount.Sac.Branch.id'], 'prefix' => false));
		}
		elseif(isset($object['Sac.Branch.id']))
		{
			$parts['Branch'] = $object['Sac.Branch.shortname'];
			if(!$asText)
				$parts['Branch'] = $this->Html->link($parts['Branch'], array('controller' => 'branches', 'action' => 'view', $object['Sac.Branch.id'], 'prefix' => false));
		}
		elseif(isset($object['Branch.id']))
		{
			$parts['Branch'] = $object['Branch.shortname'];
			if(!$asText)
				$parts['Branch'] = $this->Html->link($parts['Branch'], array('controller' => 'branches', 'action' => 'view', $object['Branch.id'], 'prefix' => false));
		}
		
		if(isset($object['AdAccount.Sac.Branch.Division.id']))
		{
			$parts['Division'] = $object['AdAccount.Sac.Branch.Division.shortname'];
			if(!$asText)
				$parts['Division'] = $this->Html->link($parts['Division'], array('controller' => 'divisions', 'action' => 'view', $object['AdAccount.Sac.Branch.Division.id'], 'prefix' => false));
		}
		elseif(isset($object['Sac.Branch.Division.id']))
		{
			$parts['Division'] = $object['Sac.Branch.Division.shortname'];
			if(!$asText)
				$parts['Division'] = $this->Html->link($parts['Division'], array('controller' => 'divisions', 'action' => 'view', $object['Sac.Branch.Division.id'], 'prefix' => false));
		}
		elseif(isset($object['Branch.Division.id']))
		{
			$parts['Division'] = $object['Branch.Division.shortname'];
			if(!$asText)
				$parts['Division'] = $this->Html->link($parts['Division'], array('controller' => 'divisions', 'action' => 'view', $object['Branch.Division.id'], 'prefix' => false));
		}
		elseif(isset($object['Division.id']))
		{
			$parts['Division'] = $object['Division.shortname'];
			if(!$asText)
				$parts['Division'] = $this->Html->link($parts['Division'], array('controller' => 'divisions', 'action' => 'view', $object['Division.id'], 'prefix' => false));
		}
		
		if(isset($object['AdAccount.Sac.Branch.Division.Org.id']))
		{
			$parts['Org'] = $object['AdAccount.Sac.Branch.Division.Org.shortname'];
			if(!$asText)
				$parts['Org'] = $this->Html->link($parts['Org'], array('controller' => 'orgs', 'action' => 'view', $object['AdAccount.Sac.Branch.Division.Org.id'], 'prefix' => false));
		}
		elseif(isset($object['Sac.Branch.Division.Org.id']))
		{
			$parts['Org'] = $object['Sac.Branch.Division.Org.shortname'];
			if(!$asText)
				$parts['Org'] = $this->Html->link($parts['Org'], array('controller' => 'orgs', 'action' => 'view', $object['Sac.Branch.Division.Org.id'], 'prefix' => false));
		}
		elseif(isset($object['Branch.Division.Org.id']))
		{
			$parts['Org'] = $object['Branch.Division.Org.shortname'];
			if(!$asText)
				$parts['Org'] = $this->Html->link($parts['Org'], array('controller' => 'orgs', 'action' => 'view', $object['Branch.Division.Org.id'], 'prefix' => false));
		}
		elseif(isset($object['Division.Org.id']))
		{
			$parts['Org'] = $object['Division.Org.shortname'];
			if(!$asText)
				$parts['Org'] = $this->Html->link($parts['Org'], array('controller' => 'orgs', 'action' => 'view', $object['Division.Org.id'], 'prefix' => false));
		}
		elseif(isset($object['Org.id']))
		{
			$parts['Org'] = $object['Org.shortname'];
			if(!$asText)
				$parts['Org'] = $this->Html->link($parts['Org'], array('controller' => 'orgs', 'action' => 'view', $object['Org.id'], 'prefix' => false));
		}
		
		$parts = array_reverse($parts);
		
		return implode('/', $parts);
	}
	
	public function getClosestCrm($object = array(), $asText = false)
	{
		// support for Cerberus' Fisma Systems/Inventories
		if(isset($object['FismaSystem']['OwnerContact']))
		{
			$object['AdAccount'] = $object['FismaSystem']['OwnerContact'];
		}
		
		$object = Hash::flatten($object);
		
		$ad_account_id = false;
		$ad_account_name = false;
		
		if(!$ad_account_id and isset($object['AdAccount.Sac.SacCrm.id']) and $object['AdAccount.Sac.SacCrm.id'])
		{
			$ad_account_id = $object['AdAccount.Sac.SacCrm.id'];
			$ad_account_name = $object['AdAccount.Sac.SacCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Sac.SacCrm.id']) and $object['Sac.SacCrm.id'])
		{
			$ad_account_id = $object['Sac.SacCrm.id'];
			$ad_account_name = $object['Sac.SacCrm.name'];
		}
		
		if(!$ad_account_id and isset($object['AdAccount.Sac.Branch.BranchCrm.id']) and $object['AdAccount.Sac.Branch.BranchCrm.id'])
		{
			$ad_account_id = $object['Sac.Branch.BranchCrm.id'];
			$ad_account_name = $object['Sac.Branch.BranchCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Sac.Branch.BranchCrm.id']) and $object['Sac.Branch.BranchCrm.id'])
		{
			$ad_account_id = $object['Sac.Branch.BranchCrm.id'];
			$ad_account_name = $object['Sac.Branch.BranchCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Branch.BranchCrm.id']) and $object['Branch.BranchCrm.id'])
		{
			$ad_account_id = $object['Branch.BranchCrm.id'];
			$ad_account_name = $object['Branch.BranchCrm.name'];
		}
		
		if(!$ad_account_id and isset($object['AdAccount.Sac.Branch.Division.DivisionCrm.id']) and $object['AdAccount.Sac.Branch.Division.DivisionCrm.id'])
		{
			$ad_account_id = $object['AdAccount.Sac.Branch.Division.DivisionCrm.id'];
			$ad_account_name = $object['AdAccount.Sac.Branch.Division.DivisionCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Sac.Branch.Division.DivisionCrm.id']) and $object['Sac.Branch.Division.DivisionCrm.id'])
		{
			$ad_account_id = $object['Sac.Branch.Division.DivisionCrm.id'];
			$ad_account_name = $object['Sac.Branch.Division.DivisionCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Branch.Division.DivisionCrm.id']) and $object['Branch.Division.DivisionCrm.id'])
		{
			$ad_account_id = $object['Branch.Division.DivisionCrm.id'];
			$ad_account_name = $object['Branch.Division.DivisionCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Division.DivisionCrm.id']) and $object['Division.DivisionCrm.id'])
		{
			$ad_account_id = $object['Division.DivisionCrm.id'];
			$ad_account_name = $object['Division.DivisionCrm.name'];
		}
		
		if(!$ad_account_id and isset($object['AdAccount.Sac.Branch.Division.Org.OrgCrm.id']) and $object['AdAccount.Sac.Branch.Division.Org.OrgCrm.id'])
		{
			$ad_account_id = $object['AdAccount.Sac.Branch.Division.Org.OrgCrm.id'];
			$ad_account_name = $object['AdAccount.Sac.Branch.Division.Org.OrgCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Sac.Branch.Division.Org.OrgCrm.id']) and $object['Sac.Branch.Division.Org.OrgCrm.id'])
		{
			$ad_account_id = $object['Sac.Branch.Division.Org.OrgCrm.id'];
			$ad_account_name = $object['Sac.Branch.Division.Org.OrgCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Branch.Division.Org.OrgCrm.id']) and $object['Branch.Division.Org.OrgCrm.id'])
		{
			$ad_account_id = $object['Branch.Division.Org.OrgCrm.id'];
			$ad_account_name = $object['Branch.Division.Org.OrgCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Division.Org.OrgCrm.id']) and $object['Division.Org.OrgCrm.id'])
		{
			$ad_account_id = $object['Division.Org.OrgCrm.id'];
			$ad_account_name = $object['Division.Org.OrgCrm.name'];
		}
		elseif(!$ad_account_id and isset($object['Org.OrgCrm.id']) and $object['Org.OrgCrm.id'])
		{
			$ad_account_id = $object['Org.OrgCrm.id'];
			$ad_account_name = $object['Org.OrgCrm.name'];
		}
		
		$out = false;
		
		if($ad_account_id and $ad_account_name)
		{
			$out = $ad_account_name;
			if(!$asText)
				$out = $this->Html->link($out, array('controller' => 'ad_accounts', 'action' => 'view', $ad_account_id));
		}
		
		return $out;
	}
	
	public function linkAdAccount($object = array(), $url = array(), $options = array(), $alias = false)
	{
		if(!$object)
			return false;
		
		if(!$alias)
			$alias = 'AdAccount';
		
		if(!isset($object[$alias]))
			return false;
		
		if(!isset($object[$alias]['id']))
			return false;
		
		if(!isset($object[$alias]['username']))
			return false;
		
		$url = array_merge(array(
			'controller' => 'ad_accounts', 'action' => 'view', $object[$alias]['id'], 'prefix' => false
		), $url);
		
		if(!isset($options['class']))
			$options['class'] = '';
		$options['class'] .= ' contacts-adaccount-link';
		
		return $this->Html->link($object[$alias]['username'], $url, $options);
	}
	
	public function findTabCount($tableRow = array())
	{
		$out = 0;
		$results = Hash::extract($tableRow, '{s}.{n}.url[#=/ui-tabs-/].#');
		if($results)
		{
			$values = array();
			foreach($results as $result)
			{
				$matches = array();
				if(preg_match('/ui-tabs-(\d+)$/', $result, $matches))
					if(isset($matches[1]))
						$values[$matches[1]] = $matches[1];
			}
			$out = max($values);
		}
		return $out;
	}
	
	public function listDirectorOf($list = array(), $asText = false, $usefullname = false)
	{
		if(!$list)
			return array();
		
		$out = array();
		
		if(isset($list['sacs']) and $list['sacs'])
		{
			$out['sacs'] = array();
			foreach($list['sacs'] as $sac)
			{
				$item = $sac['Sac']['shortname'];
				if($usefullname)
					$item = $sac['Sac']['name'];
					
				if(!$asText)
					$item = $this->Html->link($item, array('controller' => 'sacs', 'action' => 'view', $sac['Sac']['id'] ));
				$out['sacs'][] = $item;
			}
		}
		
		if(isset($list['branches']) and $list['branches'])
		{
			$out['branches'] = array();
			foreach($list['branches'] as $branch)
			{
				$item = $branch['Branch']['shortname'];
				if($usefullname)
					$item = $branch['Branch']['name'];
				if(!$asText)
					$item = $this->Html->link($item, array('controller' => 'branches', 'action' => 'view', $branch['Branch']['id'] ));
				$out['branches'][] = $item;
			}
		}
		
		if(isset($list['divisions']) and $list['divisions'])
		{
			$out['divisions'] = array();
			foreach($list['divisions'] as $division)
			{
				$item = $division['Division']['shortname'];
				if($usefullname)
					$item = $division['Division']['name'];
				if(!$asText)
					$item = $this->Html->link($item, array('controller' => 'divisions', 'action' => 'view', $division['Division']['id'] ));
				$out['divisions'][] = $item;
			}
		}
		
		if(isset($list['orgs']) and $list['orgs'])
		{
			$out['orgs'] = array();
			foreach($list['orgs'] as $org)
			{
				$item = $org['Org']['shortname'];
				if($usefullname)
					$item = $org['Org']['name'];
				if(!$asText)
					$item = $this->Html->link($item, array('controller' => 'orgs', 'action' => 'view', $org['Org']['id'] ));
				$out['orgs'][] = $item;
			}
		}
		return $out;
	}
}