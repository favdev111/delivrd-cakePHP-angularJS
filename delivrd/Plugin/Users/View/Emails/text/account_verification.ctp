<?php
/**
 * Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010 - 2013, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

echo __d('users', 'Hello %s,', $user[$model]['email']);
echo "\n";
echo __d('users', 'To validate your account, you must visit the URL below within 24 hours');
echo "\n";
echo Router::url(array('admin' => false, 'plugin' => 'users', 'controller' => 'users', 'action' => 'add', 'slug' => $user[$model]['token']), true);
echo "\n\n";
echo __d('users', 'Good luck');
