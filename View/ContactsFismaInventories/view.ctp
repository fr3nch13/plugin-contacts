<?php 

$_page_options = (isset($page_options)?$page_options:[]);
$_details_left = (isset($details_left)?$details_left:[]);
$_details_right = (isset($details_right)?$details_right:[]);
$_stats = (isset($stats)?$stats:[]);
$_tabs = (isset($tabs)?$tabs:[]);

$page_options = [];

$page_options = array_merge($page_options, $_page_options);

$details_left = [];
$details_left[] = ['name' => __('Friendly Name'), 'value' => $record['FismaInventory']['name']];
$details_left[] = ['name' => __('MAC Address'), 'value' => $record['FismaInventory']['mac_address']];
$details_left[] = ['name' => __('Asset Tag'), 'value' => $record['FismaInventory']['asset_tag']];
$details_left[] = ['name' => __('DNS Name'), 'value' => $record['FismaInventory']['dns_name']];
$details_left[] = ['name' => __('IP Address'), 'value' => $record['FismaInventory']['ip_address']];
$details_left[] = ['name' => __('NAT IP Address'), 'value' => $record['FismaInventory']['nat_ip_address']];
$details_left = array_merge($details_left, $_details_left);

$details_right = [];
$details_right[] = ['name' => __('Location'), 'value' => $record['FismaInventory']['location']];
$details_right[] = ['name' => __('Purpose'), 'value' => $record['FismaInventory']['purpose']];
$details_right[] = ['name' => __('FISMA System'), 'value' => $this->Html->link($record['FismaSystem']['name'], ['controller' => 'fisma_systems', 'action' => 'view', $record['FismaSystem']['id']])];
$details_right[] = ['name' => __('URL'), 'value' => $record['FismaInventory']['url']];
$details_right[] = ['name' => __('Created'), 'value' => $this->Wrap->niceTime($record['FismaInventory']['created'])];
$details_right[] = ['name' => __('Modified'), 'value' => $this->Wrap->niceTime($record['FismaInventory']['modified'])];
$details_right = array_merge($details_right, $_details_right);


$stats = [];
$tabs = [];

$stats = array_merge($stats, $_stats);
$tabs = array_merge($tabs, $_tabs);

/*
$tabs['assoc_accounts'] = $stats['assoc_accounts'] = array(
	'id' => 'assoc_accounts',
	'name' => __('Associated Accounts'),
	'tip' => __('%s assigned to them.', __('Associated Accounts')),
	'ajax_url' => array('controller' => 'assoc_accounts', 'action' => 'ad_account', $adAccount['AdAccount']['id']),
);
*/

$record_name = $record['FismaInventory']['name'];
if(!$record_name)
	$record_name = $record['FismaInventory']['asset_tag'];
if(!$record_name)
	$record_name = $record['FismaInventory']['dns_name'];
if(!$record_name)
	$record_name = $record['FismaInventory']['ip_address'];
if(!$record_name)
	$record_name = $record['FismaInventory']['mac_address'];

echo $this->element('Utilities.page_compare', [
	'page_title' => __('%s: %s', __('FISMA Inventory'), $record_name),
	'page_subtitle2' => $this->Contacts->makePath($record),
	'page_options' => $page_options,
	'details_left_title' => ' ',
	'details_left' => $details_left,
	'details_right_title' => ' ',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
]);