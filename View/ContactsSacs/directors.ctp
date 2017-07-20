<?php

$th = array();
$th['Sac.shortname'] = array('content' => __('SAC'));
$th['Sac.director_id'] = array('content' => __('SAC Director'), 'options' => array('editable' => array('type' => 'select', 'options' => $adAccounts) ) );
$th['Branch.shortname'] = array('content' => __('Branch'));
$th['Branch.director_id'] = array('content' => __('Branch Director'), 'options' => array('editable' => array('type' => 'select', 'options' => $adAccounts) ));
$th['Division.shortname'] = array('content' => __('Division'));
$th['Division.director_id'] = array('content' => __('Division Director'), 'options' => array('editable' => array('type' => 'select', 'options' => $adAccounts) ));
$th['Org.shortname'] = array('content' => __('ORG/IC'));
$th['Org.director_id'] = array('content' => __('ORG/IC Director'), 'options' => array('editable' => array('type' => 'select', 'options' => $adAccounts) ));

foreach ($sacs as $i => $sac)
{
	$td[$i] = array();
	
	$sacDirector = (isset($sac['SacDirector']['id'])?$this->Html->link($sac['SacDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['SacDirector']['id'])):'');
	if($sacDirector)
		$sacDirector = array($sacDirector, array('value' => $sac['SacDirector']['id']));
	$branchDirector = (isset($sac['Branch']['BranchDirector']['id'])?$this->Html->link($sac['Branch']['BranchDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['Branch']['BranchDirector']['id'])):'');
	if($branchDirector)
		$branchDirector = array($branchDirector, array('value' => $sac['Branch']['BranchDirector']['id']));
	$divisionDirector = (isset($sac['Branch']['Division']['DivisionDirector']['id'])?$this->Html->link($sac['Branch']['Division']['DivisionDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['Branch']['Division']['DivisionDirector']['id'])):'');
	if($divisionDirector)
		$divisionDirector = array($divisionDirector, array('value' => $sac['Branch']['Division']['DivisionDirector']['id']));
	$orgDirector = (isset($sac['Branch']['Division']['Org']['OrgDirector']['id'])?$this->Html->link($sac['Branch']['Division']['Org']['OrgDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['Branch']['Division']['Org']['OrgDirector']['id'])):'');
	if($orgDirector)
		$orgDirector = array($orgDirector, array('value' => $sac['Branch']['Division']['Org']['OrgDirector']['id']));
	
	$td[$i]['Sac.shortname'] = (isset($sac['Sac']['shortname'])?$this->Html->link($sac['Sac']['shortname'], array('controller' => 'sacs', 'action' => 'view', $sac['Sac']['id'])):'');
	$td[$i]['Sac.director_id'] = $sacDirector;
	$td[$i]['Branch.shortname'] = (isset($sac['Branch']['shortname'])?$this->Html->link($sac['Branch']['shortname'], array('controller' => 'branches', 'action' => 'view', $sac['Branch']['id'])):'');
	$td[$i]['Branch.director_id'] = $branchDirector;
	$td[$i]['Division.shortname'] = (isset($sac['Branch']['Division']['shortname'])?$this->Html->link($sac['Branch']['Division']['shortname'], array('controller' => 'divisions', 'action' => 'view', $sac['Branch']['Division']['id'])):'');
	$td[$i]['Division.director_id'] = $divisionDirector;
	$td[$i]['Org.shortname'] = (isset($sac['Branch']['Division']['Org']['shortname'])?$this->Html->link($sac['Branch']['Division']['Org']['shortname'], array('controller' => 'orgs', 'action' => 'view', $sac['Branch']['Division']['Org']['id'])):'');
	$td[$i]['Org.director_id'] = $orgDirector;
	
	
	$td[$i]['edit_id'] = array(
		'Sac' => $sac['Sac']['id'],
	);
	if(isset($sac['Branch']['id']))
		$td[$i]['edit_id']['Branch'] = $sac['Branch']['id'];
	if(isset($sac['Branch']['Division']['id']))
		$td[$i]['edit_id']['Division'] = $sac['Branch']['Division']['id'];
	if(isset($sac['Branch']['Division']['Org']['id']))
		$td[$i]['edit_id']['Org'] = $sac['Branch']['Division']['Org']['id'];
}

$use_gridedit = false;
if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$use_gridedit = true;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All Directors'),
	'th' => $th,
	'td' => $td,
	'use_pagination' => false,
	'use_search' => false,
	'use_gridedit' => $use_gridedit,
));