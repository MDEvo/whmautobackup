<?php

/**
 * Treat each server as an array with the keys name, host, username, auth_type and password
 */
$servers = [
    [
        'name'  =>  'srv1', // must be a unique name (for display purposes only)
        'host'  =>  'https://cpanelhost.com:2087',
        'username'  =>  'my_username',
        'auth_type' =>  'password', // set to 'hash' if you want to use your access hash
        'password'  =>  'my_password' // password for whm, or your access hash
    ],
    [
        'name'  =>  'srv2', // must be a unique name (for display purposes only)
        'host'  =>  'https://cpanelhost2.com:2087',
        'username'  =>  'my_username',
        'auth_type' =>  'password', // set to 'hash' if you want to use your access hash
        'password'  =>  'my_password' // password for whm, or your access hash
    ]
];

/**
 * See https://documentation.cpanel.net/display/SDK/cPanel+API+1+Functions+-+Fileman%3A%3Afullbackup for args
 */
$destination = [
    'dest'  =>  'scp',
    'server'    =>  'remote.backup.com',
    'user'  =>  'my_username',
    'password'  =>  'my_password',
    'email' =>  'alert_email@yahoo.com',
    'port'  =>  21,
    'rdir'  =>  '/path/to/backups'
];

/**
 * Mailgun Settings. Comment-out the entire array to disable.
 */
$mail = [
    'domain'    =>  'your.mailgun.domain',
    'key'   =>  'key-12344464367478568y',
    'from'	=>	'alerts@yourserver.com',
    'to' =>  'receive@gmail.com'
];