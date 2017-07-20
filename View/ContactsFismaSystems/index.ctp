<?php 

$page_title = (isset($page_title)?$page_title:false);
$page_subtitle = (isset($page_subtitle)?$page_subtitle:false);
$_page_options = (isset($page_options)?$page_options:[]);
$_page_options2 = (isset($page_options2)?$page_options2:[]);

$use_gridedit = (isset($use_gridedit)?$use_gridedit:false);
$search_placeholder = (isset($search_placeholder)?$search_placeholder:$this->Form->defaultModel);
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
$th['FismaSystem.id'] = ['content' => __('ID'), 'options' => ['sort' => 'FismaSystem.id' ]];
$th['FismaSystem.path'] = ['content' => __('Path')];
$th['FismaSystem.name'] = ['content' => __('Name'), 'options' => ['sort' => 'FismaSystem.name', 'editable' => ['type' => 'text']]];
$th['FismaSystem.fullname'] = ['content' => __('Full Name'), 'options' => ['sort' => 'FismaSystem.fullname', 'editable' => ['type' => 'text']]];
$th = array_merge($th, $_th);
$th['FismaSystem.count_children'] = ['content' => __('# Children')];
$th['FismaSystem.count_inventory'] = ['content' => __('# Inventory')];
$th['actions'] = ['content' => __('Actions'), 'options' => ['class' => 'actions']];

$td = $_td;
$totals_row = [];
$totals = [];
foreach ($records as $i => $record)
{
	$actions = array(
		$this->Html->link(__('View'), ['controller' => 'fisma_systems', 'action' => 'view', $record['FismaSystem']['id']]),
	);
	
	$td[$i] = [];
	$td[$i]['FismaSystem.id'] = $this->Html->link($record['FismaSystem']['id'], ['controller' => 'fisma_systems', 'action' => 'view', $record['FismaSystem']['id'], 'admin' => false]);
	$td[$i]['FismaSystem.path'] = $this->Contacts->makePath($record);
	$td[$i]['FismaSystem.name'] = $this->Html->link($record['FismaSystem']['name'], ['controller' => 'fisma_systems', 'action' => 'view', $record['FismaSystem']['id'], 'admin' => false]);
	$td[$i]['FismaSystem.fullname'] = $this->Html->link($record['FismaSystem']['fullname'], ['controller' => 'fisma_systems', 'action' => 'view', $record['FismaSystem']['id'], 'admin' => false]);
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
	
	$td[$i]['FismaSystem.count_children'] = ['.', [
		'ajax_count_url' => ['controller' => 'fisma_systems', 'action' => 'my_children', $record['FismaSystem']['id']],
		'url' => ['action' => 'view', $record['FismaSystem']['id'], 'tab' => 'my_children'],
	]];
	$td[$i]['FismaSystem.count_inventory'] = ['.', [
		'ajax_count_url' => ['controller' => 'fisma_inventories', 'action' => 'fisma_system', $record['FismaSystem']['id']],
		'url' => ['action' => 'view', $record['FismaSystem']['id'], 'tab' => 'fisma_system'],
	]];
		
	$td[$i]['actions'] = [
		implode("", $actions),
		['class' => 'actions'],
	];
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
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