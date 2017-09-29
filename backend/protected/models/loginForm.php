<?php
/**
 * Created by JetBrains PhpStorm.
 * User: PC
 * Date: 21.11.12
 * Time: 1:47
 * To change this template use File | Settings | File Templates.
 */
class loginForm extends CFormModel
{

    public $login;
    public $table='User';
    public $password;
    public $rememberMe = true;
    private $_identity;

    public function rules()
    {
        return array(
            array('login, password', 'required',),
            array('password', 'authenticate'),
        );
    }

    public function authenticate($attribute,$params)
    {
        $this->_identity=new UserIdentity($this->login,$this->password);
        if(!$this->_identity->authenticate()){

            $this->addError('login','Ќеправильное им€ пользовател€ или пароль.');
        }
    }

    public function Login() {
        /** @var $login определено правилами присваивани€ this->rules()*/
        /** @var $password определено правилами присваивани€ this->rules()*/

        $identity=new UserIdentity($this->login, $this->password);
        $identity->authenticate();


        switch($identity->errorCode)
        {
            case UserIdentity::ERROR_NONE: Yii::app()->user->login($identity); return true; break;
            case UserIdentity::ERROR_PASSWORD_INVALID: $this->addError('login','Ќеправильное им€ пользовател€ или пароль.'); return false;    break;
            case UserIdentity::ERROR_USERNAME_INVALID: $this->addError('password','Ќеправильное им€ пользовател€ или пароль.'); return false;     break;
        }

    }

    public function attributeLabels()
    {
        return array(

            'password'=>'ѕароль.',
            'login'=>'Ћогин.',
        );
    }






}
