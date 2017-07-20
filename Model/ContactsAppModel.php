<?php

App::uses('AppModel', 'Model');

class ContactsAppModel extends AppModel 
{
	public $schemaName = 'cakephp_plugin_contacts';
	
	public $hasMany = array();
	public $_hasMany = array();
	public $belongsTo = array();
	public $_belongsTo = array();
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Contacts.Contacts',
		'Snapshot.Stat' => array(
			'stats' => true, // must also have the snapshotStats method below for this to work.
			'entities' => array(
				'all' => array(),
				'created' => array(),
				'modified' => array(),
			),
		),
		'Utilities.Shell',
		'Utilities.Email',
	);
	
	public $cacheQueries = true;
	
	public $searchFields = array();
	public $_searchFields = array();
	
	public $autocompleteMap = array();
    public $_autocompleteMap = array(
    	'recursive' => 0,
		'fields' => array(), // list of fields to search against
		'value' => false, // the fields to use as the display value
		'data' => false, // the fields to use as the display value
		'group' => false, // the field used to group results together
		'include_data' => false, // include the data result in the display value
		'scope' => array(), // initial restriction in the query. basically the initial $conditions
    );
    
    // fields that we use to find duplicates
    public $duplicateFields = array(
    	'shortname', 'username', 'userid', 'email'
    );
	
	public function __construct($id = false, $table = null, $ds = null)
	{
		$this->hasMany = array_merge($this->_hasMany, $this->hasMany);
		$this->belongsTo = array_merge($this->_belongsTo, $this->belongsTo);
		$this->searchFields = array_merge($this->_searchFields, $this->searchFields);
		$this->autocompleteMap = array_merge($this->_autocompleteMap, $this->autocompleteMap);
		
		parent::__construct($id, $table, $ds);
		
		$this->fixVirtualFields();
	}
	
	public function beforeFind($query = array())
	{
		$this->fixVirtualFields();
		return parent::beforeFind($query);
	}
	
	public function beforeSave($options = array()) 
	{
		if(isset($this->data[$this->alias]))
		{
			foreach($this->data[$this->alias] as $k => $v)
			{
				if(!is_array($v))
					$this->data[$this->alias][$k] = trim($v);
				
				if($k == 'director_id' and !$v)
					$this->data[$this->alias][$k] = 0;
			}
		}
		
		// see if we have to make a shortname
		$schema = $this->schema();
		
		if(isset($schema['shortname']) and isset($this->data[$this->alias]['name']) and !isset($this->data[$this->alias]['shortname']))
		{
			$this->data[$this->alias]['shortname'] = $this->makeShortname($this->data[$this->alias]['name']);
		}
		
		return parent::beforeSave($options);
	}
	
	public function fixVirtualFields()
	{
		if($this->virtualFields)
		{
			foreach($this->virtualFields as $name => $value)
			{
				$this->virtualFields[$name] = str_replace($this->name, $this->alias, $value);
			}
		}
		
		// fix the related virtual fields
		foreach($this->belongsTo as $k => $belongsTo)
		{
			if($k != $belongsTo['className'])
			{
				if($this->{$k}->virtualFields)
				{
					foreach($this->{$k}->virtualFields as $name => $value)
					{
						$this->{$k}->virtualFields[$name] = str_replace($belongsTo['className'], $k, $value);
					}
				}
			}
		}
	}
	
	public function setDataSource($dataSource = null) 
	{
		$this->currentSchemaName = $this->schemaName;
		parent::setDataSource($dataSource);
		$this->schemaName = $this->currentSchemaName;
	}
	
	public function getSchemaName() 
	{
		return $this->schemaName;
	}
	
	public function makeShortname($name = false)
	{
		if(!$name)
			return false;
		
		$name = strtolower($name);
		$nameParts = preg_split('/\s+/', $name);
		
		$stopwords = array('and', 'of', 'the', '&', '&amp;');
		
		$shortname = '';
		
		foreach($nameParts as $i => $namePart)
		{
			if(in_array($namePart, $stopwords))
			{
				continue;
			}
			$shortname .= substr($namePart, 0, 1);
		}
		$shortname = strtoupper($shortname);
		return $shortname;
	}
	
	public function fixNname($name = false)
	{
		if(!$name)
			return false;
		
		$name = strtolower($name);
		$name = ucwords($name);
		$nameParts = preg_split('/\s+/', $name);
		
		$name = implode(' ', $nameParts);
		
		return $name;
	}
	
	public function getShortname($id = false)
	{
		if(!$id) return false;
		
		if($shortname = $this->field('shortname', array($this->alias.'.'.$this->primaryKey => $id)))
		{
			return $shortname;
		}
		return false;
	}
	
	public function makeDefaultFields()
	{
		$schema = $this->schema();
		$schema = array_keys($schema);
		$defaults = array_flip($schema);
		
		foreach($defaults as $field => $value)
		{
			if($field == $this->primaryKey)
			{
				unset($defaults[$field]);
				continue;
			}
			$defaults[$field] = false;
		}
		return $defaults;
	}
	
	public function autocompleteLookup($query = false)
	{
		if(!$this->autocompleteMap)
			return false;
		if(!isset($this->autocompleteMap['fields']))
			return false;
		if(!$query)
			return false;
		
		$valueModel = $this->alias;
		$valueField = $this->displayField;
		if(isset($this->autocompleteMap['value']) and $this->autocompleteMap['value'])
			$valueField = $this->autocompleteMap['value'];
		if(!$valueField)
			return false;
		if(stripos($valueField, '.'))
			list($valueModel, $valueField) = pluginSplit($valueField);
		
		$dataModel = $this->alias;
		$dataField = $this->displayField;
		if(isset($this->autocompleteMap['data']) and $this->autocompleteMap['data'])
			$dataField = $this->autocompleteMap['data'];
		if(stripos($dataField, '.'))
			list($dataModel, $dataField) = pluginSplit($dataField);
		
		$groupModel = false;
		$groupField = false;
		if(isset($this->autocompleteMap['group']) and $this->autocompleteMap['group'])
			$groupField = $this->autocompleteMap['group'];
		if(stripos($groupField, '.'))
			list($groupModel, $groupField) = pluginSplit($groupField);
		
		// out should match what is defined here: 
		// https://github.com/devbridge/jQuery-Autocomplete
		
		$out = array(
			'query' => $query,
			'suggestions' => array(),
		);
		
		$conditions = array();
		if(isset($this->autocompleteMap['scope']) and $this->autocompleteMap['scope'])
			$conditions = $this->autocompleteMap['scope'];
		
		$order = array();
		
		foreach($this->autocompleteMap['fields'] as $field)
		{
			// make sure the field includes the model
			if(!stripos($field, '.'))
				$field = $this->alias.'.'.$field;
			
			if(count($this->autocompleteMap['fields']) > 1)
				$conditions['OR'][$field. ' LIKE'] = '%'.$query.'%';
			else
				$conditions[$field. ' LIKE'] = '%'.$query.'%';
			
			$order[$field] = 'ASC';
		}
		
		$this->recursive = -1;
		if(isset($this->autocompleteMap['recursive']))
			$this->recursive = $this->autocompleteMap['recursive'];
		
		$results = $this->find('all', array('conditions' => $conditions, 'order' => $order));
		
		foreach($results as $record)
		{
			$thisSuggestion = array('value' => false, 'data' => false, 'record' => $record);
			
			if($valueModel and isset($record[$valueModel][$valueField]))
				$thisSuggestion['value'] = $record[$valueModel][$valueField];
				
			if($dataModel and $dataField and isset($record[$dataModel][$dataField]))
				$thisSuggestion['data'] = $record[$dataModel][$dataField];
			else
				$thisSuggestion['data'] = $record[$dataModel][$valueField];
			
			if($thisSuggestion['data'] and isset($this->autocompleteMap['include_data']) and $this->autocompleteMap['include_data'])
			{
				$thisSuggestion['value'] = __('(%s) %s', $thisSuggestion['data'], $thisSuggestion['value']);
			}
			
			if($groupModel and $groupField and isset($record[$groupModel][$groupField]))
			{
				$thisSuggestion['data'] = array('group' => $record[$groupModel][$groupField], 'value' => $thisSuggestion['data']);
			}
			
			$out['suggestions'][] = $thisSuggestion;
		}
		
		return $out;
	}
	
	public function duplicateConditions($conditions = array())
	{
		$groupBy = array();
		$schema = $this->schema();
		$fields = array_keys($schema);
		
		$groupFields = array_intersect($fields, $this->duplicateFields);
		
		foreach($groupFields as $field)
		{
			// get the ids that have multiple counts
			$dupes = $this->find('list', array(
				'fields' => array($this->alias.'.'. $field, $this->alias.'.'. $field),
				'group' => $this->alias.'.'.$field.' HAVING COUNT(*) >= 2',
			));
			foreach($dupes as $i => $dupe)
				if(!$dupe)
					unset($dupes[$i]);
			$conditions['OR'][$this->alias.'.'. $field] = $dupes;
		}
		return $conditions;
	}
	
	public function idsForCrm($ad_account_id = false)
	{
		$schema = $this->schema();
		
		if(!isset($schema['crm_id']))
			return array();
		
		if(!$ids = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.crm_id' => $ad_account_id,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
			'order' => array($this->alias.'.id' => 'ASC'),
		))) { return array(); }
		
		return $ids;
	}
	
	public function crmIdsForId($ids = array())
	{
		if(!$ids)
			return array();
		
		if(!isset($schema['crm_id']))
			return array();
		
		if(!$ids = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.id' => $ids,
			),
			'fields' => array($this->alias.'.crm_id', $this->alias.'.crm_id'),
			'order' => array($this->alias.'.crm_id' => 'ASC'),
		))) { return array(); }
		
		return $ids;
	}
	
	public function idsForDirector($ad_account_id = false)
	{
		$schema = $this->schema();
		
		if(!isset($schema['director_id']))
			return array();
		
		if(!$ids = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.director_id' => $ad_account_id,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
			'order' => array($this->alias.'.id' => 'ASC'),
		))) { return array(); }
		
		return $ids;
	}
	
	public function directorIdsForId($ids = array())
	{
		if(!$ids)
			return array();
		
		if(!isset($schema['director_id']))
			return array();
		
		if(!$ids = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.id' => $ids,
			),
			'fields' => array($this->alias.'.director_id', $this->alias.'.director_id'),
			'order' => array($this->alias.'.director_id' => 'ASC'),
		))) { return array(); }
		
		return $ids;
	}
}