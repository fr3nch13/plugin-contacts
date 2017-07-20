<?php

App::uses('Helper', 'View');
App::uses('AppHelper', 'View/Helper');

class ContactsAppHelper extends AppHelper 
{
	public $helpers = array(
		'Ajax', 'Time', 'Js' => array('JqueryUi'),
		'Form' => array('className' => 'Utilities.FormExt' ),
		'Html' => array('className' => 'Utilities.HtmlExt' ),
		'Number',
	);
}