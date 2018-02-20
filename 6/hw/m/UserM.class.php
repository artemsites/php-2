<?php
/**
 * Класс для работы с пользователем.
 */
class UserM
{
    protected $user_id, $user_login, $user_name, $user_password;
    
    public function __construct(){}
    
    /**
     * Конкатенация хэшей пароля и имени и переворачивания наоборот с помощью strrev()
     * 
     * @param string $name Имя
     * @param string $password Пароль
     * @return string Возвращает хэшированый логин+имя и перевернутый
     */
    public function setPass($login, $password) {
	   return strrev(md5($login) . md5($password));
    }
    
    public function getUser($id)
    {
        $res = PdoM::Instance() -> Select('users', 'id', $id);
        return $res;
    }
    
    /**
     * Метод регистрации пользователя.
     * @param string $name Имя пользователя
     * @param string $login Никнейм пользователя
     * @param string $password Мудреный хешированый конкатенированный с именем и реверсивный пароль из за $this->setPass($name, $password)
     * @return boolean
     */
    public function regUser($name, $login, $password) 
    {
        $res = PdoM::Instance() -> Select('users', 'login', $login);
        if (!$res) {
            $password = $this -> setPass($login, $password);
            $object = [
              'name' => $name,
              'login' => $login,
              'password' => $password
            ];
            $res = PdoM::Instance() -> Insert('users', $object);
            if (is_numeric($res)) { // Если вставка совершилась то вернется id то есть номер строки в таблице.
//                 var_dump($res);
                return "regUser(): Регистрация прошла успешно.";
                
            } else {
                return "regUser(): Регистрация прервалась ошибкой.";
            }
        } else { // Если вставка не совершилась то вернется массив c той строкой которая уже зарегистрирована.
//             var_dump($res);
            return "regUser(): Пользователь уже существует.";
        }
    }
    
    /**
     * Метод класса модели который обрабатывает вход пользователя.
     * 
     * @param string $login
     * @param string $password
     * @return string
     */
    public function login($login, $password) 
    {
        $res = PdoM::Instance() -> Select('users', 'login', $login);
        if ($res) {
            if ($res['password'] == $this -> setPass($login, $password)) {
                $_SESSION['user_id'] = $res['id'];
                return 'Добро пожаловать в систему, ' . $res['name'] . '!';
            } else {
                return 'Пароль не верный!';
            }
        } else {
            return 'Пользователь с таким логином не зарегистрирован!';
        }  
    }
    
    public function logout()
    {
	if (isset($_SESSION["user_id"])) {
	    unset($_SESSION["user_id"]);
	    session_destroy();
	    return true;
	} else {
	    return false;
	}
                      
    }
}