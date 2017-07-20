<?php 

$_page_options = (isset($page_options)?$page_options:array());
$_details = (isset($details)?$details:array());
$_stats = (isset($stats)?$stats:array());
$_tabs = (isset($tabs)?$tabs:array());

$page_options = array(
	'edit' => $this->Html->link(__('Edit'), array('action' => 'edit', $adAccount['AdAccount']['id'])),
);

$page_options = array_merge($page_options, $_page_options);

$details = array(
	'AdAccount.name' => array('name' => __('Name'), 'value' => $adAccount['AdAccount']['name']),
	'AdAccount.username' => array('name' => __('Username'), 'value' => $adAccount['AdAccount']['username']),
	'AdAccount.email' => array('name' => __('Email'), 'value' => $this->Html->link($adAccount['AdAccount']['email'], 'mailto:'. $adAccount['AdAccount']['email'])),
	'AdAccount.userid' => array('name' => __('NIH ID'), 'value' => $this->Html->userLink($adAccount['AdAccount']['userid'])),
	'AdAccount.phone_number' => array('name' => __('Phone'), 'value' => $adAccount['AdAccount']['phone_number']),
	'AdAccount.created' => array('name' => __('Created'), 'value' => $this->Wrap->niceTime($adAccount['AdAccount']['created'])),
	'AdAccount.modified' => array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($adAccount['AdAccount']['modified'])),
);
$details = array_merge($details, $_details);

$stats = array();
$tabs = array();

$stats = array_merge($stats, $_stats);
$tabs = array_merge($tabs, $_tabs);

$tabs['assoc_accounts'] = $stats['assoc_accounts'] = array(
	'id' => 'assoc_accounts',
	'name' => __('Associated Accounts'),
	'tip' => __('%s assigned to them.', __('Associated Accounts')),
	'ajax_url' => array('controller' => 'assoc_accounts', 'action' => 'ad_account', $adAccount['AdAccount']['id']),
);
$tabs['org_director'] = $stats['org_director'] = array(
	'id' => 'org_director',
	'name' => __('Director of %s', __('ORGs/ICs')),
	'ajax_url' => array('controller' => 'orgs', 'action' => 'director', $adAccount['AdAccount']['id']),
);
$tabs['org_crm'] = $stats['org_crm'] = array(
	'id' => 'org_crm',
	'name' => __('CRM of %s', __('ORGs/ICs')),
	'ajax_url' => array('controller' => 'orgs', 'action' => 'crm', $adAccount['AdAccount']['id']),
);
$tabs['division_director'] = $stats['division_director'] = array(
	'id' => 'division_director',
	'name' => __('Director of %s', __('Divisions')),
	'ajax_url' => array('controller' => 'divisions', 'action' => 'director', $adAccount['AdAccount']['id']),
);
$tabs['division_crm'] = $stats['division_crm'] = array(
	'id' => 'division_crm',
	'name' => __('CRM of %s', __('Divisions')),
	'ajax_url' => array('controller' => 'divisions', 'action' => 'crm', $adAccount['AdAccount']['id']),
);
$tabs['branch_director'] = $stats['branch_director'] = array(
	'id' => 'branch_director',
	'name' => __('Director of %s', __('Branches')),
	'ajax_url' => array('controller' => 'branches', 'action' => 'director', $adAccount['AdAccount']['id']),
);
$tabs['branch_crm'] = $stats['branch_crm'] = array(
	'id' => 'branch_crm',
	'name' => __('CRM of %s', __('Branches')),
	'ajax_url' => array('controller' => 'branches', 'action' => 'crm', $adAccount['AdAccount']['id']),
);
$tabs['sac_director'] = $stats['sac_director'] = array(
	'id' => 'sac_director',
	'name' => __('Director of %s', __('Sacs')),
	'ajax_url' => array('controller' => 'sacs', 'action' => 'director', $adAccount['AdAccount']['id']),
);
$tabs['sac_crm'] = $stats['sac_crm'] = array(
	'id' => 'sac_crm',
	'name' => __('CRM of %s', __('Sacs')),
	'ajax_url' => array('controller' => 'sacs', 'action' => 'crm', $adAccount['AdAccount']['id']),
);
$tabs['tags'] = $stats['tags'] = array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'ad_account', $adAccount['AdAccount']['id']),
);
$tabs['notes'] = array(
	'key' => 'notes',
	'title' => __('Notes'),
	'content' => $this->Wrap->descView($adAccount['AdAccount']['notes']),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s', __('AD Account'), $this->Contacts->makePath($adAccount)),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));