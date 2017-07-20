<?php 
// File: Plugin/Contacts/View/ContactsFismaInventories/index.ctp
$alias = (isset($alias)?$alias:'FismaInventory');
$inventoryAlias = (isset($inventoryAlias)?$inventoryAlias:__('FISMA Inventory'));
$inventoryAliases = (isset($inventoryAliases)?$inventoryAliases:__('FISMA Inventories'));

$page_title = (isset($page_title)?$page_title:$inventoryAliases);
$page_subtitle = (isset($page_subtitle)?$page_subtitle:false);
$_page_options = (isset($page_options)?$page_options:[]);
$_page_options2 = (isset($page_options2)?$page_options2:[]);

$use_gridedit = (isset($use_gridedit)?$use_gridedit:false);
$search_placeholder = (isset($search_placeholder)?$search_placeholder:$inventoryAliases);
$search_model = (isset($search_model)?$search_model:$this->Form->defaultModel);
$_th = (isset($th)?$th:[]);
$_td = (isset($td)?$td:[]);

// javascript for table sorting that don't use pagination
$use_jsordering = (isset($use_jsordering)?$use_jsordering:false);
$no_counts = (isset($no_counts)?$no_counts:false);

$page_options = [];

$page_options = array_merge($page_options, $_page_options);

$page_options2 = [];
$page_options2 = array_merge($page_options2, $_page_options2);

$th = [];
$th[$alias.'.id'] = ['content' => __('ID'), 'options' => ['sort' => $alias.'.id']];
$th[$alias.'.name'] = ['content' => __('Name'), 'options' => ['sort' => $alias.'.name']];
$th[$alias.'.mac_address'] = ['content' => __('MAC Address'), 'options' => ['sort' => $alias.'.mac_address']];
$th[$alias.'.asset_tag'] = ['content' => __('Asset Tag'), 'options' => ['sort' => $alias.'.asset_tag']];
$th[$alias.'.dns_name'] = ['content' => __('Host Name'), 'options' => ['sort' => $alias.'.dns_name']];
$th[$alias.'.ip_address'] = ['content' => __('IP Address'), 'options' => ['sort' => $alias.'.ip_address']];
$th[$alias.'.nat_ip_address'] = ['content' => __('NAT IP Address'), 'options' => ['sort' => $alias.'.nat_ip_address']];
$th = array_merge($th, $_th);
$th['actions'] = ['content' => __('Actions'), 'options' => ['class' => 'actions']];

$td = $_td;
$totals_row = [];
$totals = [];
foreach ($records as $i => $record)
{
	$actions = [
		$this->Html->link(__('View'), ['controller' => 'fisma_inventories', 'action' => 'view', $record[$alias]['id']]),
	];
	
	$td[$i] = [];
	$td[$i][$alias.'.id'] = $this->Html->link($record[$alias]['id'], ['controller' => 'fisma_inventories', 'action' => 'view', $record[$alias]['id']]);
	$td[$i][$alias.'.name'] = $record[$alias]['name'];
	$td[$i][$alias.'.mac_address'] = $record[$alias]['mac_address'];
	$td[$i][$alias.'.asset_tag'] = $record[$alias]['asset_tag'];
	$td[$i][$alias.'.dns_name'] = $record[$alias]['dns_name'];
	$td[$i][$alias.'.ip_address'] = $record[$alias]['ip_address'];
	$td[$i][$alias.'.nat_ip_address'] = $record[$alias]['nat_ip_address'];
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
		
	$td[$i]['actions'] = [
		implode("", $actions),
		['class' => 'actions'],
	];
}

echo $this->element('Utilities.page_index', [
	'page_title' => $page_title,
	'page_subtitle' => $page_subtitle,
	'page_options' => $page_options,
	'page_options2' => $page_options2,
	'search_placeholder' => $search_placeholder,
	'search_model' => $search_model,
	'th' => $th,
	'td' => $td,
]);