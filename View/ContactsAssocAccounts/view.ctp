<?php 
// File: Plugin/Contacts/View/ContactsAssocAccount/view.ctp
$_page_options = (isset($page_options)?$page_options:array());
$_details = (isset($details)?$details:array());
$_stats = (isset($stats)?$stats:array());
$_tabs = (isset($tabs)?$tabs:array());

$page_options = array(
	'edit' => $this->Html->link(__('Edit'), array('action' => 'edit', $assocAccount['AssocAccount']['id'])),
);
$page_options = array_merge($page_options, $_page_options);

$details = array(
	'AdAccount.username' => array('name' => __('AD Account'), 'value' => $this->Html->link($assocAccount['AdAccount']['username'], array('controller' => 'ad_accounts', 'action' => 'view', $assocAccount['AdAccount']['id']))),
	'AssocAccount.username' => array('name' => __('Username'), 'value' => $assocAccount['AssocAccount']['username']),
	'AssocAccount.name' => array('name' => __('Name'), 'value' => $assocAccount['AssocAccount']['name']),
	'AssocAccount.email' => array('name' => __('Email'), 'value' => $this->Html->link($assocAccount['AssocAccount']['email'], 'mailto:'. $assocAccount['AssocAccount']['email'])),
	'AssocAccount.phone_number' => array('name' => __('Phone'), 'value' => $assocAccount['AssocAccount']['phone_number']),
	'AssocAccount.created' => array('name' => __('Created'), 'value' => $this->Wrap->niceTime($assocAccount['AssocAccount']['created'])),
);
$details = array_merge($details, $_details);

$stats = array();
$tabs = array();

$stats = array_merge($stats, $_stats);
$tabs = array_merge($tabs, $_tabs);

$stats['tags'] = array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'assoc_account', $assocAccount['AssocAccount']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs['tags'] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'assoc_account', $assocAccount['AssocAccount']['id']),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s', __('Associated Account'), $this->Contacts->makePath($assocAccount)),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));