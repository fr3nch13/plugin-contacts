<?php ?>
<!-- File: Plugin/Contacts/View/ContactsDivision/edit.ctp -->
<div class="top">
	<h1><?php echo __('Edit %s', __('Division')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create(); ?>
		    <fieldset>
		        <legend><?php echo __('Edit %s', __('Division')); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('org_id', array(
						'label' => array(
							'text' => __('ORG/IC'),
						),
						'div' => array('class' => 'forth'),
						'empty' => __('(Empty) To Be Determined'),
						'searchable' => true,
					));
					echo $this->Form->input('shortname', array(
						'label' => __('Short Name'),
						'after' => $this->Html->tag('div', __('Shows in the main menu, and lists.'), array('class' => 'info')),
						'div' => array('class' => 'forth'),
					));
					echo $this->Form->input('name', array(
						'label' => __('Normal Name'),
						'after' => $this->Html->tag('div', __('A more descriptive name.'), array('class' => 'info')),
						'div' => array('class' => 'half'),
					));
					echo $this->Html->divClear();
					echo $this->Form->input('active', array(
						'div' => array('class' => 'third'),
						'type' => 'select',
						'options' => array(1 => __('Yes'), 0 => __('No')),
					));
					echo $this->Form->input('director_id', array(
						'label' => array(
							'text' => __('Director'),
						),
						'div' => array('class' => 'third'),
						'options' => $adAccounts,
						'empty' => __('(Empty) To Be Determined'),
						'searchable' => true,
					));
					echo $this->Form->input('crm_id', array(
						'label' => array(
							'text' => __('CRM'),
						),
						'div' => array('class' => 'third'),
						'options' => $adAccounts,
						'empty' => __('(Empty) To Be Determined'),
						'searchable' => true,
					));
					echo $this->Html->divClear();
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>