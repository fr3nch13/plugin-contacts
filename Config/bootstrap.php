<?php
/***
 *
 * Configuration settings for the Usage plugin
 *
 */

Cache::config('contacts', array(
	'engine' => 'Memcache',
    'mask' => 0666,
    'duration' => 604800, // 1 WEEK
    //'path' => CACHE,
    'prefix' => 'usage_'
));

CakeLog::config('contacts', array(
	'engine' => 'FileLog',
	'mask' => 0666,
	'size' => 0, // disable file log rotation, handled by logrotate
	'types' => array('info', 'notice', 'error', 'warning', 'debug'),
	'scopes' => array('contacts'),
	'file' => 'contacts.log',
));