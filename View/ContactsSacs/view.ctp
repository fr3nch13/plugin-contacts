<?php 
// File: Plugin/Contacts/View/ContactsSacs/view.ctp
$_page_options = (isset($page_options)?$page_options:array());
$_details = (isset($details)?$details:array());
$_stats = (isset($stats)?$stats:array());
$_tabs = (isset($tabs)?$tabs:array());

$page_options = array();

if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$page_options['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $sac['Sac']['id']));
	$page_options['duplicate'] = $this->Html->link(__('Duplicate'), array('action' => 'add', $sac['Sac']['id']));
	$page_options['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $sac['Sac']['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
}
$page_options = array_merge($page_options, $_page_options);

$details = array(
	'Sac.shortname' => array('name' => __('Short Name'), 'value' =>$sac['Sac']['shortname']),
	'Sac.name' => array('name' => __('Full Name'), 'value' =>$sac['Sac']['name']),
	'SacDirector.name' => array('name' => __('Director'), 'value' => $this->Html->link($sac['SacDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['SacDirector']['id']))),
	'SacCrm.name' => array('name' => __('CRM'), 'value' => $this->Html->link($sac['SacCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['SacCrm']['id']))),
	'Sac.created' => array('name' => __('Created'), 'value' => $this->Wrap->niceTime($sac['Sac']['created'])),
);
$details = array_merge($details, $_details);

$stats = array();
$tabs = array();

$stats = array_merge($stats, $_stats);
$tabs = array_merge($tabs, $_tabs);

$stats['AdAccounts'] = array(
	'id' => 'AdAccounts',
	'name' => __('AD Accounts'),
	'tip' => __('%s assigned to them.', __('AD Accounts')),
	'ajax_count_url' => array('controller' => 'ad_accounts', 'action' => 'sac', $sac['Sac']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['AdAccounts'] = array(
	'key' => 'AdAccounts',
	'title' => __('AD Accounts'),
	'url' => array('controller' => 'ad_accounts', 'action' => 'sac', $sac['Sac']['id']),
);

$stats['AssocAccounts'] = array(
	'id' => 'AssocAccounts',
	'name' => __('Associated'),
	'tip' => __('Associated Accounts'),
	'ajax_count_url' => array('controller' => 'assoc_accounts', 'action' => 'sac', $sac['Sac']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['AssocAccounts'] = array(
	'key' => 'AssocAccounts',
	'title' => __('Associated Accounts'),
	'url' => array('controller' => 'assoc_accounts', 'action' => 'sac', $sac['Sac']['id']),
);

$stats['tags'] = array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'sac', $sac['Sac']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs['tags'] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'sac', $sac['Sac']['id']),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s', __('Sac'), $this->Contacts->makePath($sac)),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));