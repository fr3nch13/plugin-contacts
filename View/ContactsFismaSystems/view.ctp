<?php 

$_page_options = (isset($page_options)?$page_options:[]);
$_details_blocks = (isset($details_blocks)?$details_blocks:[]);
$_stats = (isset($stats)?$stats:[]);
$_tabs = (isset($tabs)?$tabs:[]);
$page_options = [];

$page_options = array_merge($page_options, $_page_options);

$details_blocks[1][1] = [
	'title' => __('Details'),
	'details' => [
		['name' => __('Parent'), 'value' => ($record['FismaSystemParent']['id']?$this->Html->link($record['FismaSystemParent']['name'], ['action' => 'view', $record['FismaSystemParent']['id']]):__('None'))],
		['name' => __('UUID'), 'value' => $record['FismaSystem']['uuid']],
		['name' => __('PII Records'), 'value' => (($record['FismaSystem']['pii_count'] > 0)?$record['FismaSystem']['pii_count']:' 0&nbsp;')],
		['name' => __('Rogue?'), 'value' => $this->Wrap->yesNoUnknown($record['FismaSystem']['is_rogue'])],
		['name' => __('FISMA Reportable'), 'value' => $this->Wrap->yesNoUnknown($record['FismaSystem']['fisma_reportable'])],
		['name' => __('Under Ongoing Authorization'), 'value' => $this->Wrap->yesNoUnknown($record['FismaSystem']['ongoing_auth'])],
		['name' => __('ATO Expiration'), 'value' => $this->Wrap->niceTime($record['FismaSystem']['ato_expiration'])],
		['name' => __('Created'), 'value' => $this->Wrap->niceTime($record['FismaSystem']['created'])],
		['name' => __('Modified'), 'value' => $this->Wrap->niceTime($record['FismaSystem']['modified'])],
	],
];

$systemOwner['AdAccount'] = (isset($record['OwnerContact'])?$record['OwnerContact']:[]);
$systemOwnerLink = (isset($systemOwner['AdAccount']['id'])?$this->Html->link($systemOwner['AdAccount']['name'], ['controller' => 'ad_accounts', 'action' => 'view', $systemOwner['AdAccount']['id']]):'');
$systemOwnerPath = $this->Contacts->makePath($systemOwner);

$details_blocks[1][2] = [
	'title' => __('Contacts'),
	'details' => [
		['name' => __('System Owner'), 'value' => __('%s - %s', $systemOwnerLink, $systemOwnerPath)],
	]
];

if(isset($record['OwnerContact']['Sac']['Branch']['Division']['Org']['OrgDirector']['id']) 
and $record['OwnerContact']['Sac']['Branch']['Division']['Org']['OrgDirector']['id'])
{
	$detailsValue = $this->Html->link($record['OwnerContact']['Sac']['Branch']['Division']['Org']['OrgDirector']['name'], [
		'controller' => 'ad_accounts',
		'action' => 'view',
		$record['OwnerContact']['Sac']['Branch']['Division']['Org']['OrgDirector']['id']
	]);
	$details_blocks[1][2]['details'][] = ['name' => __('%s Director', __('ORG/IC')), 'value' => $detailsValue];
}
if(isset($record['OwnerContact']['Sac']['Branch']['Division']['Org']['OrgCrm']['id']) 
and $record['OwnerContact']['Sac']['Branch']['Division']['Org']['OrgCrm']['id'])
{
	$detailsValue = $this->Html->link($record['OwnerContact']['Sac']['Branch']['Division']['Org']['OrgCrm']['name'], [
		'controller' => 'ad_accounts',
		'action' => 'view',
		$record['OwnerContact']['Sac']['Branch']['Division']['Org']['OrgCrm']['id']
	]);
	$details_blocks[1][2]['details'][] = ['name' => __('%s CRM', __('ORG/IC')), 'value' => $detailsValue];
}
if(isset($record['OwnerContact']['Sac']['Branch']['Division']['DivisionDirector']['id']) 
and $record['OwnerContact']['Sac']['Branch']['Division']['DivisionDirector']['id'])
{
	$detailsValue = $this->Html->link($record['OwnerContact']['Sac']['Branch']['Division']['DivisionDirector']['name'], [
		'controller' => 'ad_accounts',
		'action' => 'view',
		$record['OwnerContact']['Sac']['Branch']['Division']['DivisionDirector']['id']
	]);
	$details_blocks[1][2]['details'][] = ['name' => __('%s Director', __('Division')), 'value' => $detailsValue];
}
if(isset($record['OwnerContact']['Sac']['Branch']['Division']['DivisionCrm']['id']) 
and $record['OwnerContact']['Sac']['Branch']['Division']['DivisionCrm']['id'])
{
	$detailsValue = $this->Html->link($record['OwnerContact']['Sac']['Branch']['Division']['DivisionCrm']['name'], [
		'controller' => 'ad_accounts',
		'action' => 'view',
		$record['OwnerContact']['Sac']['Branch']['Division']['DivisionCrm']['id']
	]);
	$details_blocks[1][2]['details'][] = ['name' => __('%s CRM', __('Division')), 'value' => $detailsValue];
}
if(isset($record['OwnerContact']['Sac']['Branch']['BranchDirector']['id']) 
and $record['OwnerContact']['Sac']['Branch']['BranchDirector']['id'])
{
	$detailsValue = $this->Html->link($record['OwnerContact']['Sac']['Branch']['BranchDirector']['name'], [
		'controller' => 'ad_accounts',
		'action' => 'view',
		$record['OwnerContact']['Sac']['Branch']['BranchDirector']['id']
	]);
	$details_blocks[1][2]['details'][] = ['name' => __('%s Director', __('Branch')), 'value' => $detailsValue];
}
if(isset($record['OwnerContact']['Sac']['Branch']['BranchCrm']['id']) 
and $record['OwnerContact']['Sac']['Branch']['BranchCrm']['id'])
{
	$detailsValue = $this->Html->link($record['OwnerContact']['Sac']['Branch']['BranchCrm']['name'], [
		'controller' => 'ad_accounts',
		'action' => 'view',
		$record['OwnerContact']['Sac']['Branch']['BranchCrm']['id']
	]);
	$details_blocks[1][2]['details'][] = ['name' => __('%s CRM', __('Branch')), 'value' => $detailsValue];
}

$details_blocks = array_merge($details_blocks, $_details_blocks);

$stats = [];
$tabs = [];
$tabs['fisma_inventories'] = $stats['fisma_inventories'] = [
	'id' => 'fisma_inventories',
	'name' => __('Direct Inventory'), 
	'ajax_url' => ['controller' => 'fisma_inventories', 'action' => 'fisma_system', $record['FismaSystem']['id']],
];

// this system is a parent
if(!$record['FismaSystemParent']['id'])
{
	$tabs['fisma_inventories_all'] = $stats['fisma_inventories_all'] = [
		'id' => 'fisma_inventories_all',
		'name' => __('All Inventory'), 
		'ajax_url' => ['controller' => 'fisma_inventories', 'action' => 'fisma_system_all', $record['FismaSystem']['id']],
	];
	$tabs['fisma_inventories_children'] = $stats['fisma_inventories_children'] = [
		'id' => 'fisma_inventories_children',
		'name' => __('Children Inventory'), 
		'ajax_url' => ['controller' => 'fisma_inventories', 'action' => 'fisma_system_children', $record['FismaSystem']['id']],
	];
	$tabs['my_children'] = $stats['my_children'] = [
		'id' => 'my_children',
		'name' => __('Children'), 
		'ajax_url' => ['controller' => 'fisma_systems', 'action' => 'my_children', $record['FismaSystem']['id']],
	];
}

$tabs = array_merge($tabs, $_tabs);
$stats = array_merge($stats, $_stats);

$tabs['description'] = [
	'id' => 'description',
	'name' => __('Description'),
	'content' => $this->element('Utilities.page_generic', [
		'page_content' => $this->Wrap->descView($record['FismaSystem']['description']),
	]),
];
$tabs['notes'] = [
	'id' => 'notes',
	'name' => __('Notes'),
	'content' => $this->element('Utilities.page_generic', [
		'page_content' => $this->Wrap->descView($record['FismaSystem']['notes']),
	]),
];
$tabs['daar_notes'] = [
	'id' => 'daar_notes',
	'name' => __('DAA-R Notes'),
	'content' => $this->element('Utilities.page_generic', [
		'page_content' => $this->Wrap->descView($record['FismaSystem']['daar_notes']),
	]),
];
$tabs['isso_notes'] = [
	'id' => 'isso_notes',
	'name' => __('ISSO Notes'),
	'content' => $this->element('Utilities.page_generic', [
		'page_content' => $this->Wrap->descView($record['FismaSystem']['isso_notes']),
	]),
];
$tabs['fo_notes'] = [
	'id' => 'fo_notes',
	'name' => __('FO Notes'),
	'content' => $this->element('Utilities.page_generic', [
		'page_content' => $this->Wrap->descView($record['FismaSystem']['fo_notes']),
	]),
];

echo $this->element('Utilities.page_view_columns', [
	'page_title' => __('%s: %s', __('FISMA System'), $record['FismaSystem']['name']),
	'page_subtitle' => __('Full Name: %s', $record['FismaSystem']['fullname'].' '),
	'page_subtitle2' => $this->Contacts->makePath($record),
	'page_options' => $page_options,
	'stats' => $stats,
	'details_blocks' => $details_blocks,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
]);