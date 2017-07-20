<?php 
// File: Plugin/Contacts/View/ContactsOrgs/view.ctp
$_page_options = (isset($page_options)?$page_options:array());
$_details = (isset($details)?$details:array());
$_stats = (isset($stats)?$stats:array());
$_tabs = (isset($tabs)?$tabs:array());

$page_options = array();

if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$page_options['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $org['Org']['id']));
	$page_options['add'] = $this->Html->link(__('Duplicate'), array('action' => 'add', $org['Org']['id']));
	$page_options['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $org['Org']['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
}
$page_options = array_merge($page_options, $_page_options);

$details = array(
	'Org.shortname' => array('name' => __('Short Name'), 'value' =>$org['Org']['shortname']),
	'Org.name' => array('name' => __('Full Name'), 'value' =>$org['Org']['name']),
	'OrgDirector.name' => array('name' => __('Director'), 'value' => $this->Html->link($org['OrgDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $org['OrgDirector']['id']))),
	'OrgCrm.name' => array('name' => __('CRM'), 'value' => $this->Html->link($org['OrgCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $org['OrgCrm']['id']))),
	'Org.created' => array('name' => __('Created'), 'value' => $this->Wrap->niceTime($org['Org']['created'])),
);
$details = array_merge($details, $_details);

$stats = array();
$tabs = array();

$stats = array_merge($stats, $_stats);
$tabs = array_merge($tabs, $_tabs);

$stats['Divisions'] = array(
	'id' => 'Divisions',
	'name' => __('Divisions'),
	'ajax_count_url' => array('controller' => 'divisions', 'action' => 'org', $org['Org']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['Divisions'] = array(
	'key' => 'Divisions',
	'title' => __('Divisions'),
	'url' => array('controller' => 'divisions', 'action' => 'org', $org['Org']['id']),
);

$stats['Branches'] = array(
	'id' => 'Branches',
	'name' => __('Branches'),
	'ajax_count_url' => array('controller' => 'branches', 'action' => 'org', $org['Org']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['Branches'] = array(
	'key' => 'Branches',
	'title' => __('Branches'),
	'url' => array('controller' => 'branches', 'action' => 'org', $org['Org']['id']),
);

$stats['Sacs'] = array(
	'id' => 'Sacs',
	'name' => __('Sacs'),
	'ajax_count_url' => array('controller' => 'sacs', 'action' => 'org', $org['Org']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['Sacs'] = array(
	'key' => 'Sacs',
	'title' => __('Sacs'),
	'url' => array('controller' => 'sacs', 'action' => 'org', $org['Org']['id']),
);

$stats['AdAccounts'] = array(
	'id' => 'AdAccounts',
	'name' => __('AD Accounts'),
	'tip' => __('%s assigned to them.', __('AD Accounts')),
	'ajax_count_url' => array('controller' => 'ad_accounts', 'action' => 'org', $org['Org']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['AdAccounts'] = array(
	'key' => 'AdAccounts',
	'title' => __('AD Accounts'),
	'url' => array('controller' => 'ad_accounts', 'action' => 'org', $org['Org']['id']),
);

$stats['AssocAccounts'] = array(
	'id' => 'AssocAccounts',
	'name' => __('Associated'),
	'tip' => __('Associated Accounts'),
	'ajax_count_url' => array('controller' => 'assoc_accounts', 'action' => 'org', $org['Org']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['AssocAccounts'] = array(
	'key' => 'AssocAccounts',
	'title' => __('Associated Accounts'),
	'url' => array('controller' => 'assoc_accounts', 'action' => 'org', $org['Org']['id']),
);

$stats['tags'] = array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'org', $org['Org']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs['tags'] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'org', $org['Org']['id']),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s', __('ORG/IC'), $this->Contacts->makePath($org)),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));