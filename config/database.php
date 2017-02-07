<?php

return [
    'class'=>'CDbConnection',
	'connectionString'=>require(dirname(__FILE__).'/specific/db_name.php'),
	'username' => require(dirname(__FILE__).'/specific/db_login.php'),
	'password' => require(dirname(__FILE__).'/specific/db_password.php'),
];