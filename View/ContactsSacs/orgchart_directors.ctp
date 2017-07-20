<?php

$pivot_id = (isset($pivot_id)?$pivot_id:'pivot_list_'. rand(1,1000));

$page_options = array();

$this->start('page_content');

$listHtml = $this->Html->nestedList($list);

echo $this->Html->tag('div', $listHtml, array('id' => 'object-pivot-'. $pivot_id))
?>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{
	var pivotOptions = {
		orgChart: true
	};
	
	$('div#object-pivot-<?php echo $pivot_id; ?>').objectPivot(pivotOptions);
});
//]]>
</script>
<?php

$this->end();

echo $this->element('Utilities.page_generic', array(
	'page_title' => __('%s Organizational Chart', __('Directors')),
	'page_options' => $page_options,
	'page_content' => $this->fetch('page_content'),
));