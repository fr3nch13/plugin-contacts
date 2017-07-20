<?php

App::uses('ContactsAppShell', 'Contacts.Console/Command');

class ContactsShell extends ContactsAppShell
{
	// the models to use
	public $uses = array('AdAccount');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Contacts Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$parser = parent::getOptionParser();
		
		$parser->description(__d('cake_console', 'The Contacts Shell used to run cron jobs common in all of the apps for Contacts Plugin.'));
		
		$parser->addSubcommand('test_ned', array(
			'help' => __('Test the connection to the USER database.'),
			'parser' => array(
				'arguments' => array(
					'username' => array(
						'help' => __('The username to look up as a test.'),
						'short' => 'u',
						'required' => true
					),
				),
			),
		));
		
		$parser->addSubcommand('update_from_ned', array(
			'help' => __('Updates the users with information from USER.'),
		));
		
		return $parser;
	}
	
	public function test_ned()
	{
		Configure::write('debug', 1);
		$this->AdAccount->testDir($this->args[0]);
	}
	
	public function update_from_ned()
	{
		Configure::write('debug', 1);
		$this->AdAccount->updateFromDir();
	}
}