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

// App::uses('Security', 'Utility');
App::uses('UsersAppModel', 'Users.Model');
App::uses('SearchableBehavior', 'Search.Model/Behavior');
App::uses('SluggableBehavior', 'Utils.Model/Behavior');

/**
 * Users Plugin User Model
 *
 * @package User
 * @subpackage User.Model
 */
class User extends UsersAppModel {

	/**
	 * Name
	 *
	 * @var string
	 */
	public $name = 'User';

	/**
	 * Additional Find methods
	 *
	 * @var array
	 */
	public $findMethods = array(
		'search' => true
	);

	/**
	 * All search fields need to be configured in the Model::filterArgs array.
	 *
	 * @var array
	 * @link https://github.com/CakeDC/search
	 */
	public $filterArgs = array(
		'username' => array('type' => 'like'),
		'email' => array('type' => 'value')
	);

	/**
	 * Displayfield
	 *
	 * @var string $displayField
	 */
	public $displayField = 'username';

	/**
	 * Time the email verification token is valid in seconds
	 *
	 * @var integer
	 */
	public $emailTokenExpirationTime = 86400;

	public $validationDomain = 'users';
	public $pickOptions = array(1 => 'No Scanning', 2 => 'Scan Bin', 3 => 'Scan SKU/EAN', 5 => 'Scan both bin and SKU/EAN');
	public $batchOptions = array(1 => 'No Scanning', 2 => 'Scan Bin', 3 => 'Scan SKU/EAN', 4 => 'Scan both bin and SKU/EAN');
	public $invAlerts = array(1 => 'Dont send any alerts', 2 => 'Send only low inventory alerts', 3 => 'Send list of all inventory');
	public $businessType = array(0 => 'Unknown', 1 => 'Ecommerce', 2 => '3pl service provider', 3 => 'Other');

	public $actsAs = array(
       'Containable',
       'Upload.Upload' => array(
            'logo' => array(
                'deleteFolderOnDelete' => true,
                'deleteOnUpdate' => true,
                'uploadFileNameMaxSize' => 250,
                'nameCallback' => 'genName'
            )
        )
    );

	public function genName($field, $currentName) {
		$extension = pathinfo($currentName, PATHINFO_EXTENSION);
		$fileName = 'logo-'. substr(md5(time()), 0, 5) .'.'. $extension;
		return $fileName;
	}

	/**
	 * Validation parameters
	 *
	 * @var array
	 */
	public $validate = array(
		'username' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'required' => true, 'allowEmpty' => false,
				'message' => 'Please enter a username.',
                       ),
                               
			'alpha' => array(
				'rule' => array('alphaNumeric'),
				'message' => 'The username must be alphanumeric.'),
			'createName' => array(
	            'rule' => 'isValidName',
				'message' => 'This username is already in use.',
			),
			'username_min' => array(
				'rule' => array('minLength', '3'),
				'message' => 'The username must have at least 3 characters.'),
                        'username_max' => array(
				'rule' => array('maxLength', '30'),
				'message' => 'The username length can be up to 30 characters.')),
		'mobile' => array(
            'notBlank' => array(
                'rule' => 'notBlank',
                'message' => 'notEmpty',
            ),
            'mobile' => array(
               'rule' => 'numeric',
               'message' => 'Please enter a valid mobile number.',
           ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Mobile number is already exits.',
                'last' => true,
            ),
        ),
		'email' => array(
			'isValid' => array(
				'rule' => 'email',
				'required' => true,
				'message' => 'Please enter a valid email address.','on' => 'create'),
			'isUnique' => array(
				'rule' => array('isUnique', 'email'),
				'message' => 'This email is already in use.','on' => 'create')),
		'password' => array(
			'too_short' => array(
				'rule' => array('minLength', '6'),
				'message' => 'The password must have at least 6 characters.'),
                        'too_long' => array(
				'rule' => array('maxLength', '30'),
				'message' => 'The password length can be up to 30 characters.'),
			'complex' => array(
				'rule' => 'ValidPassword',
				'message' => 'Password should have at least one small letter and one digit'),
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'Please enter a password.')),
		'temppassword' => array(
			'rule' => 'confirmPassword',
			'message' => 'The passwords are not equal, please try again.'), 
		'tos' => array(
			 'notEmpty' => array(
                    'rule'     => array('comparison', '!=', 0),
                    'required' => true,
                    'message'  => 'Please check terms of services if you want to proceed.'
                )
		),
		'new_password' => array(
			'too_short' => array(
				'rule' => array('minLength', '6'),
				'message' => 'The password must have at least 6 characters.','on' => 'edit'),
                    'too_long' => array(
				'rule' => array('maxLength', '30'),
				'message' => 'The password length can be up to 30 characters.','on' => 'edit'),
                    'complex' => array(
				'rule' => 'ValidPassword',
				'message' => 'Password should have at least one small letter and one digit','on' => 'edit'),
			'required' => array(
				'rule' => 'notBlank',
				'message' => 'Please enter a password.','on' => 'edit')),	
                'confirm_password' => array(
			'rule' => 'confirmRePassword',
			'message' => 'The passwords are not equal, please try again.','on' => 'edit'),
		'logo_url' => array(
				'webaddress' => array(
					'rule' => 'url',
					//'required' => false,
					'allowEmpty' => true,
					'message' => 'Please enter a valid image URL. For example, http://www.example.com/images/p1.jpg',
				),
			),

			'logo' => array(
	            'myMime' => array(
	                'rule' => 'myMime',
	                'message' => 'You can upload only gif, jpg or png files'
	            ),
	            'fileExtention' => array (
	                'rule' => array('extension',  array('gif', 'jpeg', 'png', 'jpg')),
	                'message' => 'You can upload only gif, jpg or png files'
	            ),
	        ),
        );
			
		public $belongsTo = array(
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Currency' => array(
			'className' => 'Currency',
			'foreignKey' => 'currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Zone' => array(
			'className' => 'Zone',
			'foreignKey' => 'timezone_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Msystem' => array(
			'className' => 'Msystem',
			'foreignKey' => 'msystem_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'State' => array(
			'className' => 'State',
			'foreignKey' => 'state_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
		);
	
	public $hasMany = array(
		'Network' => array(
			'className' => 'Network',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Event' => array(
			'className' => 'Event',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'user_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Integration' => array(
			'className' => 'Integration',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
	);

	public $hasOne = array(
 		'Address' => array(
 			'className' => 'Address',
 			'foreignKey' => 'user_address_id',
 			'dependent' => true
 		),
 		'Subscription' => array(
 			'className' => 'Subscription',
 			'foreignKey' => 'user_id',
 			'dependent' => true
 		),
    );

	public $hasAndBelongsToMany = array(
        'Store' =>
            array(
                'className' => 'Store',
                'joinTable' => 'users_stores',
            )
    );

    public function isValidName($check=null){
		if(!empty(CakeSession::read("Auth.User.id"))) {
			$first = $this->find('first', array('conditions' => array('User.id' => CakeSession::read("Auth.User.id"))));

			if((!empty($first['User']['username'])) && $check['username'] === $first['User']['username']) {
				return true;
			} else {
				$count = $this->find('count', array('conditions' => array('User.username' => $check['username'])));
				if(empty($count)) {
			       return true;
			    } else {
			       return false;
			    }
			}
		}
		
	}

	public function myMime($file){
        if(!empty($_FILES['data'])) {
            if(isset($_FILES['data']['type']['User']['logo'])) {
                if(in_array($_FILES['data']['type']['User']['logo'], array('image/png', 'image/jpg', 'image/jpeg', 'image/gif'))) {
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * beforeSave
     *
     * @param array $options
     * @return boolean
     */
    public function beforeSave($options = array()) {
    	
        if (!empty($this->data['User']['password'])) {
            $this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
        }

        return true;
    }

/**
 * Constructor
 *
 * @param bool|string $id ID
 * @param string $table Table
 * @param string $ds Datasource
 */
	public function __construct($id = false, $table = null, $ds = null) {
		$this->_setupBehaviors();
		$this->_setupValidation();
		parent::__construct($id, $table, $ds);
	}

/**
 * Setup available plugins
 *
 * This checks for the existence of certain plugins, and if available, uses them.
 *
 * @return void
 * @link https://github.com/CakeDC/search
 * @link https://github.com/CakeDC/utils
 */
	protected function _setupBehaviors() {
		if (class_exists('SearchableBehavior')) {
			$this->actsAs[] = 'Search.Searchable';
		}

		if (class_exists('SluggableBehavior') && Configure::read('Users.disableSlugs') !== true) {
			$this->actsAs['Utils.Sluggable'] = array(
				'label' => 'username',
				'method' => 'multibyteSlug');
		}
	}

/**
 * Setup validation rules
 *
 * @return void
 */
	protected function _setupValidation() {
		$this->validatePasswordChange = array(
			'new_password' => $this->validate['password'],
			'confirm_password' => array(
				'required' => array('rule' => array('compareFields', 'new_password', 'confirm_password'), 'required' => true, 'message' => __d('users', 'The passwords are not equal.'))),
			'old_password' => array(
				'to_short' => array('rule' => 'validateOldPassword', 'required' => true, 'message' => __d('users', 'Invalid password.'))));
	}

/**
 * Create a hash from string using given method.
 * Fallback on next available method.
 *
 * Override this method to use a different hashing method
 *
 * @param string $string String to hash
 * @param string $type Method to use (sha1/sha256/md5)
 * @param boolean $salt If true, automatically appends the application's salt
 *	 value to $string (Security.salt)
 * @return string Hash
 */
	public function hash($string, $type = null, $salt = false) {
		return Security::hash($string, $type, $salt);
	}

/**
 * Custom validation method to ensure that the two entered passwords match
 *
 * @param string $password Password
 * @return boolean Success
 */
	public function confirmPassword($password = null) {
		if ((isset($this->data[$this->alias]['password']) && isset($password['temppassword']))
			&& !empty($password['temppassword'])
			&& ($this->data[$this->alias]['password'] === $password['temppassword'])) {
			return true;
		}
		return false;
	}

	// public function validImage($file = null) {
	//    $size = getimagesize($file['logo_url']);
	//    pr($size);die;
	//    return (strtolower(substr($size['mime'], 0, 5)) == 'image' ? true : false);  
	// }
	
	public function confirmRePassword($password = null) {
		if ((isset($this->data[$this->alias]['new_password']) && isset($password['confirm_password']))
			&& !empty($password['confirm_password'])
			&& ($this->data[$this->alias]['new_password'] === $password['confirm_password'])) {
			return true;
		}
		return false;
	}

/**
 * Compares the email confirmation
 *
 * @param array $email Email data
 * @return boolean
 */
	public function confirmEmail($email = null) {
		if ((isset($this->data[$this->alias]['email']) && isset($email['confirm_email']))
			&& !empty($email['confirm_email'])
			&& (strtolower($this->data[$this->alias]['email']) === strtolower($email['confirm_email']))) {
				return true;
		}
		return false;
	}

/**
 * Checks the token for email verification
 *
 * @param string $token
 * @return array
 */
	public function checkEmailVerfificationToken($token = null) {
		$result = $this->find('first', array(
			'contain' => array(),
			'conditions' => array(
				$this->alias . '.email_verified' => 0,
				$this->alias . '.email_token' => $token),
			'fields' => array(
				'id', 'email', 'email_token_expires', 'role')));

		if (empty($result)) {
			return false;
		}

		return $result;
	}

/**
 * Verifies a users email by a token that was sent to him via email and flags the user record as active
 *
 * @param string $token The token that wa sent to the user
 * @throws RuntimeException
 * @return array On success it returns the user data record
 */
	public function verifyEmail($token = null) {
		$user = $this->checkEmailVerfificationToken($token);

		if ($user === false) {
			throw new RuntimeException(__d('users', 'Invalid token, please check the email you were sent, and retry the verification link.'));
		}

		$expires = strtotime($user[$this->alias]['email_token_expires']);
		if ($expires < time()) {
			throw new RuntimeException(__d('users', 'The token has expired.'));
		}

		$data[$this->alias]['active'] = 1;
		$user[$this->alias]['email_verified'] = 1;
		$user[$this->alias]['email_token'] = null;
		$user[$this->alias]['email_token_expires'] = null;

		$user = $this->save($user, array(
			'validate' => false,
			'callbacks' => false));
		$this->data = $user;
		return $user;
	}

/**
 * Updates the last activity field of a user
 *
 * @param string $userId User id
 * @param string $field Default is "last_action", changing it allows you to use this method also for "last_login" for example
 * @return boolean True on success
 */
	public function updateLastActivity($userId = null, $field = 'last_action') {
		if (!empty($userId)) {
			$this->id = $userId;
		}
		if ($this->exists()) {
			return $this->saveField($field, date('Y-m-d H:i:s', time()));
		}
		return false;
	}

/**
 * Checks if an email is in the system, validated and if the user is active so that the user is allowed to reste his password
 *
 * @param array $postData post data from controller
 * @return mixed False or user data as array on success
 */
	public function passwordReset($postData = array()) {
		$user = $this->find('first', array(
			'contain' => array(),
			'conditions' => array(
				$this->alias . '.active' => 1,
				$this->alias . '.email' => $postData[$this->alias]['email'])));

		if (!empty($user) && $user[$this->alias]['email_verified'] == 1) {
			$sixtyMins = time() + 43000;
			$token = $this->generateToken();
			$user[$this->alias]['password_token'] = $token;
			$user[$this->alias]['email_token_expires'] = date('Y-m-d H:i:s', $sixtyMins);
			$user = $this->save($user, false);
			$this->data = $user;
			return $user;
		} elseif (!empty($user) && $user[$this->alias]['email_verified'] == 0) {
			$this->invalidate('email', __d('users', 'This Email Address exists but was never validated.'));
		} else {
			$this->invalidate('email', __d('users', 'This Email Address does not exist in the system.'));
		}

		return false;
	}

/**
 * Checks the token for a password change
 * 
 * @param string $token Token
 * @return mixed False or user data as array
 */
	public function checkPasswordToken($token = null) {
		
		$user = $this->find('first', array(
			'contain' => array(),
			'conditions' => array(
				$this->alias . '.active' => 1,
				$this->alias . '.password_token' => $token,
				$this->alias . '.email_token_expires >=' => date('Y-m-d H:i:s'))));
			
		if (empty($user)) {
			return false;
		}
		return $user;
	}

/**
 * Changes the validation rules for the User::resetPassword() method
 *
 * @return array Set of rules required for the User::resetPassword() method
 */
	public function setUpResetPasswordValidationRules() {
		return array(
			'new_password' => $this->validate['password'],
			'confirm_password' => array(
				'required' => array(
					'rule' => array('compareFields', 'new_password', 'confirm_password'),
					'message' => __d('users', 'The passwords are not equal.'))));
	}

/**
 * Resets the password
 * 
 * @param array $postData Post data from controller
 * @return boolean True on success
 */
	public function resetPassword($postData = array()) {
		$result = false;

		$tmp = $this->validate;
		//$this->validate = $this->setUpResetPasswordValidationRules();
		
		$this->set($postData);
		
		if (!empty($postData)) {
		
			$this->data[$this->alias]['password'] = $this->hash($this->data[$this->alias]['new_password'], null, true);
			$this->data[$this->alias]['password_token'] = null;
			$this->save($this->data, array(
				'validate' => false,
				'callbacks' => false));
			$result = true;
			return $result;
		}else{
			return $result;
		}

		//$this->validate = $tmp;
		//return $result;
	}

/**
 * Changes the password for a user
 *
 * @param array $postData Post data from controller
 * @return boolean True on success
 */
	public function changePassword($postData = array()) {
		$this->validate = $this->validatePasswordChange;

		$this->set($postData);
		if ($this->validates()) {
			$this->data[$this->alias]['password'] = $this->hash($this->data[$this->alias]['new_password'], null, true);
			$this->save($postData, array(
				'validate' => false,
				'callbacks' => false));
			return true;
		}
		return false;
	}

/**
 * Validation method to check the old password
 *
 * @param array $password
 * @throws OutOfBoundsException
 * @return boolean True on success
 */
	public function validateOldPassword($password) {
		if (!isset($this->data[$this->alias]['id']) || empty($this->data[$this->alias]['id'])) {
			if (Configure::read('debug') > 0) {
				throw new OutOfBoundsException(__d('users', '$this->data[\'' . $this->alias . '\'][\'id\'] has to be set and not empty'));
			}
		}

		$currentPassword = $this->field('password', array($this->alias . '.id' => $this->data[$this->alias]['id']));
		return $currentPassword === $this->hash($password['old_password'], null, true);
	}

/**
 * Validation method to compare two fields
 *
 * @param mixed $field1 Array or string, if array the first key is used as fieldname
 * @param string $field2 Second fieldname
 * @return boolean True on success
 */
	public function compareFields($field1, $field2) {
		if (is_array($field1)) {
			$field1 = key($field1);
		}

		if (isset($this->data[$this->alias][$field1]) && isset($this->data[$this->alias][$field2]) &&
			$this->data[$this->alias][$field1] == $this->data[$this->alias][$field2]) {
			return true;
		}
		return false;
	}

/**
 * Returns all data about a user
 *
 * @param string|integer $slug user slug or the uuid of a user
 * @param string $field
 * @throws NotFoundException
 * @return array
 */
	public function view($slug = null, $field = 'slug') {
		$user = $this->find('first', array(
			'contain' => array(),
			'conditions' => array(
				'OR' => array(
					$this->alias . '.' . $field => $slug,
					$this->alias . '.' . $this->primaryKey => $slug),
				$this->alias . '.active' => 1,
				$this->alias . '.email_verified' => 1)));

		if (empty($user)) {
			throw new NotFoundException(__d('users', 'The user does not exist.'));
		}

		return $user;
	}

/**
 * Finds an user simply by email
 *
 * Used by the following methods:
 *  - checkEmailVerification
 *  - resendVerification
 *
 * Override it as needed, to add additional models to contain for example
 *
 * @param string $email
 * @return array
 */
	public function findByEmail($email = null) {
		return $this->find('first', array(
			'contain' => array(),
			'conditions' => array(
				$this->alias . '.email' => $email,
			)
		));
	}

/**
 * Checks if an email is already verified and if not renews the expiration time
 *
 * @param array $postData the post data from the request
 * @param boolean $renew
 * @return bool True if the email was not already verified
 */
	public function checkEmailVerification($postData = array(), $renew = true) {
		$user = $this->findByEmail($postData[$this->alias]['email']);

		if (empty($user)) {
			$this->invalidate('email', __d('users', 'Invalid Email address.'));
			return false;
		}

		if ($user[$this->alias]['email_verified'] == 1) {
			$this->invalidate('email', __d('users', 'This email is already verified.'));
			return false;
		}

		if ($user[$this->alias]['email_verified'] == 0) {
			if ($renew === true) {
				$user[$this->alias]['email_token_expires'] = $this->emailTokenExpirationTime();
				$this->save($user, array(
					'validate' => false,
					'callbacks' => false,
				));
			}
			$this->data = $user;
			return true;
		}
	}

/**
 * Registers a new user
 *
 * Options:
 * - bool emailVerification : Default is true, generates the token for email verification
 * - bool removeExpiredRegistrations : Default is true, removes expired registrations to do cleanup when no cron is configured for that
 * - bool returnData : Default is true, if false the method returns true/false the data is always available through $this->User->data
 *
 * @param array $postData Post data from controller
 * @param mixed should be array now but can be boolean for emailVerification because of backward compatibility
 * @return mixed
 */
	public function register($postData = array(), $options = array()) {
		
		$Event = new CakeEvent(
			'Users.Model.User.beforeRegister',
			$this,
			array(
				'data' => $postData,
				'options' => $options
			)
		);

		$this->getEventManager()->dispatch($Event);
		if ($Event->isStopped()) {
			return $Event->result;
		}

		if (is_bool($options)) {
			$options = array('emailVerification' => $options);
		}

		$defaults = array(
			'emailVerification' => true,
			'removeExpiredRegistrations' => true,
			'returnData' => true);
		extract(array_merge($defaults, $options));

		$postData = $this->_beforeRegistration($postData, $emailVerification);

		if ($removeExpiredRegistrations) {
			$this->_removeExpiredRegistrations();
		}

		$this->set($postData);

		if ($this->validates()) {
			$postData[$this->alias]['password'] = $this->hash($postData[$this->alias]['password'], 'sha1', true);
			//$this->create();
			$this->data = $this->save($postData);
			$this->data[$this->alias]['id'] = $this->id;
			//pr($this->data[$this->alias]['id']);die;

			$Event = new CakeEvent(
				'Users.Model.User.afterRegister',
				$this,
				array(
					'data' => $this->data,
					'options' => $options
				)
			);

			$this->getEventManager()->dispatch($Event);

			if ($Event->isStopped()) {
				return $Event->result;
			}

			if ($returnData) {
				return $this->data;
			}
			return true;
		}
		return false;
	}

/**
 * Resends the verification if the user is not already validated or invalid
 *
 * @param array $postData Post data from controller
 * @return mixed False or user data array on success
 */
	public function resendVerification($postData = array()) {
		if (!isset($postData[$this->alias]['email']) || empty($postData[$this->alias]['email'])) {
			$this->invalidate('email', __d('users', 'Please enter your email address.'));
			return false;
		}

		$user = $this->findByEmail($postData[$this->alias]['email']);

		if (empty($user)) {
			$this->invalidate('email', __d('users', 'The email address does not exist in the system'));
			return false;
		}

		if ($user[$this->alias]['email_verified'] == 1) {
			$this->invalidate('email', __d('users', 'Your account is already authenticaed.'));
			return false;
		}

		if ($user[$this->alias]['active'] == 0) {
			$this->invalidate('email', __d('users', 'Your account is disabled.'));
			return false;
		}

		$user[$this->alias]['email_token'] = $this->generateToken();
		$user[$this->alias]['email_token_expires'] = $this->emailTokenExpirationTime();

		return $this->save($user, false);
	}

/**
 * Returns the time the email verification token expires
 *
 * @return string
 */
	public function emailTokenExpirationTime() {
		return date('Y-m-d H:i:s', time() + $this->emailTokenExpirationTime);
	}

/**
 * Generates a password
 *
 * @param int $length Password length
 * @return string
 */
	public function generatePassword($length = 10) {
		srand((double)microtime() * 1000000);
		$password = '';
		$vowels = array("a", "e", "i", "o", "u");
		$cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr",
							"cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");
		for ($i = 0; $i < $length; $i++) {
			$password .= $cons[mt_rand(0, 31)] . $vowels[mt_rand(0, 4)];
		}
		return substr($password, 0, $length);
	}

/**
 * Generate token used by the user registration system
 *
 * @param int $length Token Length
 * @return string
 */
	public function generateToken($length = 10) {
		$possible = '0123456789abcdefghijklmnopqrstuvwxyz';
		$token = "";
		$i = 0;

		while ($i < $length) {
			$char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
			if (!stristr($token, $char)) {
				$token .= $char;
				$i++;
			}
		}
		return $token;
	}

/**
 * Optional data manipulation before the registration record is saved
 *
 * @param array post data array
 * @param boolean Use email generation, create token, default true
 * @return array
 */
	protected function _beforeRegistration($postData = array(), $useEmailVerification = true) {
		if ($useEmailVerification == true) {
			$postData[$this->alias]['email_token'] = $this->generateToken();
			$postData[$this->alias]['email_token_expires'] = date('Y-m-d H:i:s', time() + 86400);
		} else {
			$postData[$this->alias]['email_verified'] = 1;
		}
		$postData[$this->alias]['active'] = 1;
		$defaultRole = Configure::read('Users.defaultRole');
		if ($defaultRole) {
			$postData[$this->alias]['role'] = $defaultRole;
		} else {
			$postData[$this->alias]['role'] = 'registered';
		}
		return $postData;
	}

/**
 * Returns the search data - Requires the CakeDC Search plugin to work
 *
 * @param string $state Find State
 * @param string $query Query options
 * @param array|string $results Result data
 * @throws MissingPluginException
 * @return array
 * @link https://github.com/CakeDC/search
 */
	protected function _findSearch($state, $query, $results = array()) {
		if (!class_exists('SearchableBehavior')) {
			throw new MissingPluginException(array('plugin' => 'Utils'));
		}

		if ($state == 'before') {
			$this->Behaviors->load('Containable', array(
				'autoFields' => false)
			);
			$results = $query;

			if (empty($query['search'])) {
				$query['search'] = '';
			}

			$by = $query['by'];
			$like = '%' . $query['search'] . '%';

			switch ($by) {
				case 'username':
					$results['conditions'] = Set::merge(
						$query['conditions'],
						array($this->alias . '.username LIKE' => $like));
					break;
				case 'email':
					$results['conditions'] = Set::merge(
						$query['conditions'],
						array($this->alias . '.email LIKE' => $like));
					break;
				case 'any':
					$results['conditions'] = Set::merge(
						$query['conditions'],
						array('OR' => array(
							array($this->alias . '.username LIKE' => $like),
							array($this->alias . '.email LIKE' => $like))));
					break;
				case '' :
					$results['conditions'] = $query['conditions'];
					break;
				default :
					$results['conditions'] = Set::merge(
						$query['conditions'],
						array($this->alias . '.username LIKE' => $like));
					break;
			}

			if (isset($query['operation']) && $query['operation'] == 'count') {
				$results['fields'] = array('COUNT(DISTINCT ' . $this->alias . '.id)');
			}

			return $results;
		} elseif ($state == 'after') {
			if (isset($query['operation']) && $query['operation'] == 'count') {
				if (isset($query['group']) && is_array($query['group']) && !empty($query['group'])) {
					return count($results);
				}
				return $results[0][0]['COUNT(DISTINCT ' . $this->alias . '.id)'];
			}
			return $results;
		}
	}

/**
 * Customized paginateCount method
 *
 * @param array $conditions Find conditions
 * @param int $recursive Recursive level
 * @param array $extra Extra options
 * @return array
 */
	public function paginateCount($conditions = array(), $recursive = 0, $extra = array()) {
		$parameters = compact('conditions');
		if ($recursive != $this->recursive) {
			$parameters['recursive'] = $recursive;
		}
		if (isset($extra['type']) && isset($this->findMethods[$extra['type']])) {
			$extra['operation'] = 'count';
			return $this->find($extra['type'], array_merge($parameters, $extra));
		} else {
			return $this->find('count', array_merge($parameters, $extra));
		}
	}

/**
 * Adds a new user, to be called from admin like user roles or interfaces
 *
 * This method is not sending any email like the register() method, its simply
 * adding a new user record and sets a default role.
 *
 * The difference to register() is that this method here is intended to be used
 * by admins to add new users without going through all the registration logic
 *
 * @param array post data, should be Controller->data
 * @return boolean True if the data was saved successfully.
 */
	public function add($postData = null) {
		if (!empty($postData)) {
			$this->data = $postData;
			if ($this->validates()) {
				if (empty($postData[$this->alias]['role'])) {
					if (empty($postData[$this->alias]['is_admin'])) {
						$defaultRole = Configure::read('Users.defaultRole');
						if ($defaultRole) {
							$postData[$this->alias]['role'] = $defaultRole;
						} else {
							$postData[$this->alias]['role'] = 'registered';
						}
					} else {
						$postData[$this->alias]['role'] = 'admin';
					}
				}
				$postData[$this->alias]['password'] = $this->hash($postData[$this->alias]['password'], 'sha1', true);
				$this->create();
				$result = $this->save($postData, false);
				if ($result) {
					$result[$this->alias][$this->primaryKey] = $this->id;
					$this->data = $result;
					return true;
				}
			}
		}
		return false;
	}

/**
 * Edits an existing user
 *
 * @param string $userId User ID
 * @param array $postData controller post data usually $this->data
 * @throws NotFoundException
 * @return mixed True on successfully save else post data as array
 */
	public function edit($userId = null, $postData = null) {
		$user = $this->getUserForEditing($userId);
		$this->set($user);
		if (empty($user)) {
			throw new NotFoundException(__d('users', 'Invalid User'));
		}

		if (!empty($postData)) {
			$this->set($postData);
			$result = $this->save(null, true);
			if ($result) {
				$this->data = $result;
				return true;
			} else {
				return $postData;
			}
		}
	}

/**
 * Gets the user data that needs to be edited
 *
 * Override this method and inject the conditions you need
 */
	public function getUserForEditing($userId = null, $options = array()) {
		$defaults = array(
			'contain' => array(),
			'conditions' => array($this->alias . '.id' => $userId));
		$options = Set::merge($defaults, $options);

		$user = $this->find('first', $options);

		if (empty($user)) {
			throw new NotFoundException(__d('users', 'Invalid User'));
		}

		return $user;
	}

/**
 * Removes all users from the user table that are outdated
 *
 * Override it as needed for your specific project
 *
 * @return void
 */
	protected function _removeExpiredRegistrations() {
		$this->deleteAll(array(
			$this->alias . '.email_verified' => 0,
			$this->alias . '.email_token_expires <' => date('Y-m-d H:i:s')));
	}
        
        public function ValidPassword($check) {
	        // $data array is passed using the form field name as the key
	        // have to extract the value to make the function generic
	        $value = array_values($check);
	        $value = $value[0];

	        //$uppercase = preg_match('@[A-Z]@', $value);
	        $lowercase = preg_match('@[a-z]@', $value);
	        $number    = preg_match('@[0-9]@', $value);
	        //$specialchars = preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $value);
	      	//  var_dump($value);
	        // exit();
	        if(!$lowercase || !$number || strlen($value) < 6 ) {
	       	 	return false; 
	        } else {
	            return true;
			}
		}

	public function afterSave($created, $options = array()) {
		if(!$created) {

		}
	}

	public function updateRole($user_id) {
		$user = $this->find('first', ['conditions' => array('id' => $user_id), 'fields' => array('paid', 'locationsactive', 'role'), 'requrcive' => false]);
		$productcount = $this->Product->find('count', array('conditions' => ['Product.user_id' => $user_id, 'Product.status_id NOT IN' => [12, 13], 'Product.deleted' =>0]));
		
		if( $productcount > 10 && $user['User']['paid'] && $user['User']['locationsactive'] ) {

		} else {
			if($user['User']['role'] == 'trial') {
				//stop trial
			}
		}
	}

	public function getAuthUser($user_id) {
		$user = $this->find('first', [
            'conditions' => array('User.id' => $user_id),
            'contain' => array('Subscription')
        ]);
        return $user;
	}
}
