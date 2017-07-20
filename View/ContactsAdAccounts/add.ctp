<?php 
// File: Plugin/Contacts/View/ContactsAdAccounts/add.ctp

$form_id = (isset($form_id)?$form_id:rand(1,1000));
?>
<div class="top">
	<h1><?php echo __('Create new %s', __('AD Account')); ?></h1>
</div>
<div class="center">
	<div class="form" id="object-form-<?php echo $form_id; ?>">
		<?php echo $this->Form->create(); ?>
		    <fieldset>
		        <legend><?php echo __('%s Information', __('AD Account')); ?></legend>
		    	<?php
				echo $this->Form->input('username', array(
					'div' => array('class' => 'half'),
				));
				echo $this->Form->input('name', array(
					'div' => array('class' => 'half'),
				));
				echo $this->Form->input('email', array(
					'div' => array('class' => 'half'),
				));
				echo $this->Form->input('userid', array(
					'label' => __('User ID'),
					'div' => array('class' => 'half'),
				));
				echo $this->Form->input('phone_number', array(
					'div' => array('class' => 'forth'),
				));
				echo $this->Form->input('sac_id', array(
					'div' => array('class' => 'threeforths'),
					'empty' => __('(Empty) To Be Determined'),
					'searchable' => true,
				));
				echo $this->Form->input('notes');
				echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{
	var formOptions = {
	};
	
	var formInstance = $('div#object-form-<?php echo $form_id; ?>').objectForm(formOptions);
	
	$('#AdAccountUsername').on('blur', function(event)
	{
		if(!$(this).val())
			return true;
		// disable all un-disabled fields
		$('div#object-form-<?php echo $form_id; ?> input:enabled').addClass('temp-disabled').prop( "disabled", true );
		$('div#object-form-<?php echo $form_id; ?> select:enabled').addClass('temp-disabled').prop( "disabled", true );
		$('div#object-form-<?php echo $form_id; ?> select[searchable]').trigger("chosen:updated");
		
		formInstance.data('nihfo-objectForm').ajax({
			url: '<?= $this->Html->url($this->Html->urlModify(array("action" => "user_info"))) ?>.json',
			dataType: 'json',
			method: 'POST',
			data: { username: $(this).val() },
			success: function(data) {
				// fill out the other forms items
				if(data.result.userid && $('#AdAccountUserid').length && !$('#AdAccountUserid').val())
					$('#AdAccountUserid').val(data.result.userid);
				if(data.result.name && $('#AdAccountName').length && !$('#AdAccountName').val())
					$('#AdAccountName').val(data.result.name);
				if(data.result.email && $('#AdAccountEmail').length && !$('#AdAccountEmail').val())
					$('#AdAccountEmail').val(data.result.email);
				if(data.result.phone_number && $('#AdAccountPhoneNumber').length && !$('#AdAccountPhoneNumber').val())
					$('#AdAccountPhoneNumber').val(data.result.phone_number);
				if(data.result.sac_id && $('#AdAccountSacId').length && !$('#AdAccountSacId').val())
				{
					$('#AdAccountSacId').val(data.result.sac_id);
					if($('#AdAccountSacId[searchable]').length)
						$("#AdAccountSacId").trigger("chosen:updated");
				}
			},
			complete: function(data, textStatus, jqXHR) {
				$('div#object-form-<?php echo $form_id; ?> input:disabled.temp-disabled').prop( "disabled", false ).removeClass('temp-disabled');
				$('div#object-form-<?php echo $form_id; ?> select:disabled.temp-disabled').prop( "disabled", false ).removeClass('temp-disabled');
				$('div#object-form-<?php echo $form_id; ?> select[searchable]').trigger("chosen:updated");
			}
		});
	});
	

});
//]]>
</script>