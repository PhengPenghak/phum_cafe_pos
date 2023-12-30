<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 2;
    const STATUS_ACTIVE = 1;

    private static $_instance = NULL;

    private static $is_got_actions = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public $password, $current_password, $new_password, $confirm_password, $file, $verifyCode, $bio;
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['email', 'username', 'phone_number'], 'unique'],
            [['email', 'username', 'user_type_id'], 'required', 'on' => ['update', 'profile', 'create']],
            [['user_type_id', 'status', 'img_id', 'is_online', 'vendor_id'], 'integer'],
            [['email', 'password_hash', 'password_reset_token', 'auth_key', 'verification_token', 'username', 'default_lang', 'phone_number', 'first_name', 'last_name', 'device_type'], 'string'],
            [['created_at', 'updated_at', 'last_login', 'file'], 'safe'],

            [['password', 'new_password', 'confirm_password', 'current_password'], 'string', 'min' => 6],
            [['current_password'], 'required', 'on' => ['security']],
            ['current_password', function ($attribute, $params, $validator) {
                $user = self::findOne(Yii::$app->user->identity->id);
                if (empty($this->current_password) || !$user->validatePassword($this->current_password)) {
                    $this->addError($attribute, 'Incorrect old password.');
                }
            }],
            ['new_password', 'required', 'when' => function ($model) {
                return $model->current_password != '';
            }, 'whenClient' => "function (attribute, value) {
                return $('#user-current_password').val() != '';
            }"],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password', 'skipOnEmpty' => false, 'message' => "Passwords don't match.", 'on' => ['security']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_type_id' => 'User Type',
            'phone_number' => Yii::t('app', 'Mobile'),
            'current_password' => Yii::t('app', 'Current Password'),
            'new_password' => Yii::t('app', 'New Password'),
            'confirm_password' => Yii::t('app', 'Confirm Password'),
            'email' => 'Email',
            'created_at' => 'Created Date',
            'updated_at' => 'Updated Date',
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'status' => 'Status',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
            } else {
                $this->updated_at = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {

        function checkEmail($email)
        {
            $find1 = strpos($email, '@');
            $find2 = strpos($email, '.');
            return ($find1 !== false && $find2 !== false && $find2 > $find1);
        }

        if (checkEmail($username)) {
            return static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE]);
        } else {
            return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
        }
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE,
            'user_type_id' => 5
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . md5(time());
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getNameById($id)
    {
        $user = self::findOne($id);
        return $user ? $user->first_name . ' ' . $user->last_name : '';
    }

    public static function getUserPermission($controller_id)
    {
        $user_id = Yii::$app->user->getId();
        if ($user_id) {
            $sql = "SELECT a.controller, a.action, a.extra_action
                    FROM user_type_action AS a
                    INNER JOIN user_type_permission as b ON b.action_id = a.id
                    INNER JOIN user_type as c ON c.id = b.user_type_id
                    WHERE a.controller = :controller_id
                    AND c.id = :user_type_id";

            $user_type_id = Yii::$app->user->identity->user_type_id;
            $command = Yii::$app->db->createCommand($sql);
            $command->bindParam(":user_type_id", $user_type_id);
            $command->bindParam(":controller_id", $controller_id);
            $list = $command->queryAll();

            $array = array('signup', 'login', 'error', 'logout', 'validation', 'filter', 'dependent', 'allotment-action', 'assign-to-product', 'export', 'icon', 'ajust-markup');
            foreach ($list as $row) {
                array_push($array, $row["action"]);
                $extra_actions =  explode(",", $row["extra_action"]);
                foreach ($extra_actions as $ex) {
                    array_push($array, $ex);
                }
            }
            return $array;
        } else {
            return  array('login');
        }
    }

    /**
     * this function using to get all action from table "user_type_action" and then store it in instance, 
     * bcuz we don't need to get it again from database when we call this function again and again
     * @return array    array of actions
     * @author rachhen <rachhen.it@gmail.com>
     */
    public static function getAllAction()
    {
        $user_id = Yii::$app->user->getId();
        if (!$user_id) return Yii::$app->getResponse()->redirect(\yii\helpers\Url::to(['site/login']));

        if (!self::$is_got_actions) {
            $sql = "SELECT a.controller, a.action, a.extra_action
                FROM user_type_action AS a
                INNER JOIN user_type_permission as b ON b.action_id = a.id
                INNER JOIN user_type as c ON c.id = b.user_type_id
                WHERE c.id = :user_type_id";

            $user_type_id = Yii::$app->user->identity->user_type_id;
            $command = Yii::$app->db->createCommand($sql);
            $command->bindParam(":user_type_id", $user_type_id);
            return self::$is_got_actions = $command->queryAll();
        }
        return self::$is_got_actions;
    }

    /**
     * This function using for navigation menu can show or not
     * @param  string  $controller controller name
     * @param  string  $action     action name that child of controller
     * @return boolean        
     * @author rachhen <rachhen.it@gmail.com>     
     */
    public static function isVisible($controller, $action = 'index')
    {
        $list = self::getAllAction();
        $user_id = Yii::$app->user->getId();
        $new_list = self::extraActionStringToArray($list);
        return self::isInArray($new_list, $controller, $action) || $user_id == 1;
    }

    /**
     * this function checking if controller and action in array or not
     * @param  array   $array_actions  array of action get from database 
     * @param  string  $controller     controller name you want to match in array
     * @param  string  $action         action name you want to match in array
     * @return boolean                
     */
    public static function isInArray($array_actions, $controller, $action)
    {
        foreach ($array_actions as $row) {
            if ($row['controller'] == $controller && ($row['action'] == $action || $row['extra_action'] == $action)) {
                return true;
            }
        }
        return false;
    }

    /**
     * this function convert extra action on field 'extra_action' string to array extra on by on
     * @param  array $list  list of of action get from database
     * @return array       
     */
    public static function extraActionStringToArray($list)
    {
        $actions = array();
        foreach ($list as $key => $row) {
            $extra_actions =  explode(",", $row["extra_action"]);
            foreach ($extra_actions as $exa) {
                $new_list = $list[$key];
                $new_list['extra_action'] = $exa;
                array_push($actions, $new_list);
            }
        }
        return $actions;
    }

    public static function in_array_r($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_array_r($needle, $item, $strict))) {
                return true;
            }
        }
        return false;
    }

    /**
     * using this function for checking if sub menu have on visible or not
     * @param  array  $arr_sub_action array of action you need input
     * @return boolean
     * $arr_sub_action = [
     *     [
     *         'controller' => 'contact-hotel-profile',
     *         'action' => 'index'
     *     ],
     *     [
     *         'controller' => 'contact-cruise-profile',
     *         'action' => 'index'
     *     ]
     * ]       
     */
    public static function isVisibleHead($arr_sub_actions)
    {
        if (!is_array($arr_sub_actions)) {
            throw new \yii\base\InvalidArgumentException("Please enter valid argument.");
        }
        return self::isSubVisible($arr_sub_actions);
    }

    public static function isSubVisible($arr_sub_actions)
    {
        $is_visible = false;
        foreach ($arr_sub_actions as $item) {
            if (!ArrayHelper::keyExists('controller', $item, false)) {
                throw new InvalidArgumentException('Could not find array key "controller"!');
            }

            if (!ArrayHelper::keyExists('action', $item, false)) {
                $item['action'] = 'index';
            }

            if (self::isVisible($item['controller'], $item['action'])) {
                $is_visible = true;
                break;
            }

            if (isset($item['items'])) {
                $is_visible = self::isSubVisible($item['items']);
            }
        }
        return $is_visible;
    }

    /**
     * Find one user and clean user to array
     * @param  integer $id 
     * @return array
     */
    public static function findOneAndClean($id)
    {
        $user = User::findOne($id);
        return ArrayHelper::toArray($user, [
            'common\models\User' => [
                'id',
                'name',
                'img_id',
                'avatar' => function ($user) {
                    return self::userAvatar($user['img_id']);
                }
            ]
        ]);
    }

    public function getUserAvatar($size = 'smallx2')
    {
        if (is_numeric($this->img_id) || empty($this->img_id)) {
            return Yii::$app->upload->getProfileById($this->img_id, $size);
        } else {
            return $this->img_id;
        }
    }

    /**
     * Get user avatar
     * @param  integer $img_id 
     * @return string          url avatar
     */
    private function userAvatar($img_id)
    {
        $base_url =  Yii::getAlias('@web');
        $avatar = Yii::$app->upload->getFileUrlById($img_id);
        return $avatar ? $avatar : $base_url . '/backend/uploads/empty-avatar.png';
    }
}
