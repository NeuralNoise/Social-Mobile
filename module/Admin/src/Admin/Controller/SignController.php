<?php
namespace Admin\Controller; // пространтво имен текущего контроллера

use Admin\Controller\Auth;
use Zend\View\Model\ViewModel;
use Admin\Form; // Констуктор форм
use Zend\Debug\Debug;

/**
 * Контроллер авторизации администратора
 * @package Zend Framework 2
 * @subpackage Social
 * @since PHP >=5.3.xx
 * @version 2.15
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanilav WEB
 * @license Zend Framework GUI licene
 * @filesource /module/Admin/src/Admin/Controller/SignController.php
 */
class SignController extends Auth\AbstractAuthActionController
{

    /**
     * $_lng Свойство объекта Zend l18 translator
     * @access protected
     * @var type object
     */
    protected $_lng;

    /**
     * $_authAdmin Авторизированый
     * @access protected
     * @var type object
     */
    protected $_authAdmin = null;
    
    /**
     * zfService() Менеджер зарегистрированных сервисов ZF2
     * @access public
     * @return ServiceManager
     */
    public function zfService()
    {
        return $this->getServiceLocator();
    }

    /**
     * loginAction()  Авторизация админа
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function loginAction()
    {
        $this->_lng = $this->zfService()->get('MvcTranslator'); // загружаю переводчик
        $request    = $this->getRequest(); // Запрос через форму

        $authForm = new Form\AuthForm($this->_lng); // Форма авторизации

        //Делаю проверку на авторизацию

        $this->_authAdmin = $this->zfService()->get('authentification.Service');
        // если уже авторизирован, выносим его отсюда
        if($this->_authAdmin->hasIdentity()) return $this->redirect()->toRoute('admin');

        /**
         * Проверяю, когда форма была отправлена
         */
        if($request->isPost())
        {
            $authValidator = $this->zfService()->get('adminAuth.Validator'); // валидатор формы авторизации
            $authForm->setInputFilter($authValidator->getInputFilter()); // устанавливаю фильтры на форму авторизации
            $authForm->setData($request->getPost());
            if($authForm->isValid())
            {
                // Все отлично! Форма валидна
                $fromResult = $authForm->getData();
                
                // теперь проверяю по базе пользователей
                // вытягиваю сервис авторизации
                
                $signModel = $this->zfService()->get('sign.Model');
                $userModel = $this->zfService()->get('user.Model');
                
                $auth   =   $signModel->signAuth($userModel->getID($fromResult['login']), $fromResult['password'], $remember = 0);
                $id     =   $this->_authAdmin->getIdentity();

                if($auth && $userModel->checkRole($id, 4)) return $this->redirect()->toRoute('admin'); // успешная авторизация
                else
                {
                    // ошибка при авторизации
                    $this->flashMessenger()->addErrorMessage("Authentication failed! Wrong Login or Password");
                }
            }
        }
        else
        {
            /**
            * Делаю проверку на авторизацию
            */
            $id     =   $this->_authAdmin->getIdentity();
            if($this->_authAdmin->hasIdentity()&& $user->checkRole($id, 4))
            {
                // если уже авторизирован, выносим его отсюда
                $this->flashMessenger()->addErrorMessage("You are already authorized. If need to do sign with another account please re-authorize");
            }            
        }
            
        // Устанавливаю заголовок со страницы
        $renderer = $this->zfService()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->_lng->translate('Control Panel', 'admin').' | '.$this->_lng->translate('Social Mobile', 'default'));
            
        $view = new ViewModel(
                [
                    'authForm'      =>  $authForm, // форма авторизации
                    'errorMsgAuth'  =>  $this->flashMessenger()->getErrorMessages(), // сообщения об ошибках
                ]
        );
        return $view;
    }
    
    /**
     * logoutAction() Выход из Панели Управления
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function postAction()
    {
	// Проверяю POST это или нет
	$request    = $this->getRequest(); // Запрос через форму
	
        if($request->isPost()) //  пошла POST форма
        {
	    $arrPost = $request->getPost()->toArray();	
	    print_r($arrPost);
	    exit('sas');	    
	}
	else
	{
	    // Создаю сообщение об ошибке
	    $this->flashMessenger()->addErrorMessage('Action error! You can not send form without POST!');
	}
	return $this->redirect()->toRoute('admin');
    }
    
    /**
     * logoutAction() Выход из Панели Управления
     * @access public
     * @return \Zend\View\Model\ViewModel
     */
    public function logoutAction() {
        $this->zfService()->get('sign.Model')->getSessionStorage()->forgetMe(); // очищаю запоминание
        $this->zfService()->get('sign.Model')->getAuthService()->clearIdentity(); // очищаю весь слой авторизции    
        return $this->redirect()->toRoute('admin-auth');
    }
}