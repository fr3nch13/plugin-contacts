<?php

$th = array();
$th['Sac.shortname'] = array('content' => __('SAC'));
$th['Sac.crm_id'] = array('content' => __('SAC CRM'), 'options' => array('editable' => array('type' => 'select', 'options' => $adAccounts) ) );
$th['Branch.shortname'] = array('content' => __('Branch'));
$th['Branch.crm_id'] = array('content' => __('Branch CRM'), 'options' => array('editable' => array('type' => 'select', 'options' => $adAccounts) ));
$th['Division.shortname'] = array('content' => __('Division'));
$th['Division.crm_id'] = array('content' => __('Division CRM'), 'options' => array('editable' => array('type' => 'select', 'options' => $adAccounts) ));
$th['Org.shortname'] = array('content' => __('ORG/IC'));
$th['Org.crm_id'] = array('content' => __('ORG/IC CRM'), 'options' => array('editable' => array('type' => 'select', 'options' => $adAccounts) ));

foreach ($sacs as $i => $sac)
{
	$td[$i] = array();
	
	$sacCrm = (isset($sac['SacCrm']['id'])?$this->Html->link($sac['SacCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['SacCrm']['id'])):'');
	if($sacCrm)
		$sacCrm = array($sacCrm, array('value' => $sac['SacCrm']['id']));
	$branchCrm = (isset($sac['Branch']['BranchCrm']['id'])?$this->Html->link($sac['Branch']['BranchCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['Branch']['BranchCrm']['id'])):'');
	if($branchCrm)
		$branchCrm = array($branchCrm, array('value' => $sac['Branch']['BranchCrm']['id']));
	$divisionCrm = (isset($sac['Branch']['Division']['DivisionCrm']['id'])?$this->Html->link($sac['Branch']['Division']['DivisionCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['Branch']['Division']['DivisionCrm']['id'])):'');
	if($divisionCrm)
		$divisionCrm = array($divisionCrm, array('value' => $sac['Branch']['Division']['DivisionCrm']['id']));
	$orgCrm = (isset($sac['Branch']['Division']['Org']['OrgCrm']['id'])?$this->Html->link($sac['Branch']['Division']['Org']['OrgCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['Branch']['Division']['Org']['OrgCrm']['id'])):'');
	if($orgCrm)
		$orgCrm = array($orgCrm, array('value' => $sac['Branch']['Division']['Org']['OrgCrm']['id']));
	
	$td[$i]['Sac.shortname'] = (isset($sac['Sac']['shortname'])?$this->Html->link($sac['Sac']['shortname'], array('controller' => 'sacs', 'action' => 'view', $sac['Sac']['id']), array('title' => $sac['Sac']['name'])):'');
	$td[$i]['Sac.crm_id'] = $sacCrm;
	$td[$i]['Branch.shortname'] = (isset($sac['Branch']['shortname'])?$this->Html->link($sac['Branch']['shortname'], array('controller' => 'branches', 'action' => 'view', $sac['Branch']['id']), array('title' => $sac['Branch']['name'])):'');
	$td[$i]['Branch.crm_id'] = $branchCrm;
	$td[$i]['Division.shortname'] = (isset($sac['Branch']['Division']['shortname'])?$this->Html->link($sac['Branch']['Division']['shortname'], array('controller' => 'divisions', 'action' => 'view', $sac['Branch']['Division']['id']), array('title' => $sac['Branch']['Division']['name'])):'');
	$td[$i]['Division.crm_id'] = $divisionCrm;
	$td[$i]['Org.shortname'] = (isset($sac['Branch']['Division']['Org']['shortname'])?$this->Html->link($sac['Branch']['Division']['Org']['shortname'], array('controller' => 'orgs', 'action' => 'view', $sac['Branch']['Division']['Org']['id']), array('title' => $sac['Branch']['Division']['Org']['name'])):'');
	$td[$i]['Org.crm_id'] = $orgCrm;
	
	
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
	'page_title' => __('All CRMs'),
	'th' => $th,
	'td' => $td,
	'use_pagination' => false,
	'use_search' => false,
	'use_gridedit' => $use_gridedit,
));