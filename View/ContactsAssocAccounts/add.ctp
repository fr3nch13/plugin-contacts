<?php 
// File: Plugin/Contacts/View/ContactsAssocAccount/add.ctp
?>
<div class="top">
	<h1><?php echo __('Add %s', __('Associated Account')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create(); ?>
		    <fieldset>
		        <legend><?php echo __('%s Information', __('Associated Account')); ?></legend>
		    	<?php
				echo $this->Form->input('username', array(
					'div' => array('class' => 'third'),
				));
				echo $this->Form->input('name', array(
					'div' => array('class' => 'third'),
				));
				echo $this->Form->input('email', array(
					'div' => array('class' => 'third'),
				));
				echo $this->Form->input('phone_number', array(
					'div' => array('class' => 'forth'),
				));
				echo $this->Form->input('ad_account_id', array(
					'div' => array('class' => 'threeforths'),
					'searchable' => true,
					'empty' => __('(Empty) To Be Determined'),
				));
				echo $this->Form->input('notes');
				echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>