<?php

class ContactsFismaBehavior extends ModelBehavior 
{
	
	public function Fisma_updateRecord(Model $Model, $record = [], $checkDiff = false)
	{
	/* Update the record in our table from Cerberus's record */
		if(!$record)
			return false;
		
		$save = true;
		
		$Model->create();
		if(isset($record['id']) and $record['id'])
		{
			$Model->id = $record['id'];
			
			if($checkDiff)
			{
				$current = $Model->find('first', [
					'recursive' => -1,
					'conditions' => [$Model->alias.'.'.$Model->primaryKey => $Model->id]
				]);
				if($current)
				{
					$save = false;
					$current = $current[$Model->alias];
					
					foreach($record as $field => $value)
					{
						if(isset($current[$field]) and trim($current[$field]) != trim($record[$field]))
						{
							$save = true;
							$record['copy_updated'] = date('Y-m-d H:i:s');
						}
					}
				}
			}
		}
		
		$Model->data = $record;
		
		if($save)
		{
			return $Model->save($Model->data);
		}
		else
			return false;
	}
}