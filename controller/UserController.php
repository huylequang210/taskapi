<?php

namespace Controller;

use Model\Model;
use Model\Response;
use Auth\JwtAuthentication;

class UserController {
  protected $model;
  protected $response;
  protected $auth;
  public function __construct() {
    $this->model = new Model;
    $this->response = new Response;
    $this->auth = new JwtAuthentication;
  }

  public function createUser(array $vars) {
    $userExists = $this->model->findUser($vars['user']);
    if($userExists) {
      $this->response->setAttributes(true, 200, 'Username is already exits', [], false)->send();
    }
    $this->model->createUser($vars['user']);
    $data = array('username' => $vars['user']['username']);
    $this->response->setAttributes(true, 200, 'Insert user successfully', $data, false)->send();
  }

  public function loginUser(array $vars) {
    $user = $this->model->findUser($vars['user']);
    if(empty($user)) 
      $this->response->setAttributes(false, 401, 'Wrong username or username does not exist', $user, false)->send();
    $user['input'] = $vars['user']['password'];
    $auth = $this->auth->login($user);
    if(array_key_exists('message', $auth))
      $this->response->setAttributes(false, 401, 'Wrong password', $auth, false)->send();
    $this->response->setAttributes(true, 200, 'login successfully', $auth, false)->send();
  }

  public function deleteUser(array $vars) {
    $user = $this->model->findUser((array) $vars['authentication']);
    if(empty($user)) 
      $this->response->setAttributes(false, 401, 'Wrong username or username does not exist', $user, false)->send();
    $auth = $this->model->deleteUser($user);
    $this->response->setAttributes(true, 200, 'Delete user successfully', $vars, false)->send();
  }

  public function confirmUser(array $vars) {
    $this->response->setAttributes(true, 200, 'Confirm user', $vars, false)->send();
  }

}