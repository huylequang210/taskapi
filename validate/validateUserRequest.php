<?php

namespace Validate;

use Model\User;
use Model\Response;
use Exception;

class validateUserRequest {
  protected $data = [];
  protected $response;
  protected $httpMethod;
  protected $method;
  protected $authentication;

  public function __construct($data, $httpMethod, $method) {
    $this->data = $data;
    $this->httpMethod = $httpMethod;
    $this->method = $method;
    $this->response = new Response;
    $this->validateFunc();
  }

  public function validateFunc() {
    if($this->httpMethod === "POST" && $this->method === "createUser") {
      return $this->validateCreateUser();
    }
    if($this->httpMethod === "POST" && $this->method === "loginUser") {
      return $this->validateLoginUser();
    }
  }

  public function validateCreateUser() {
    if(!array_key_exists('username', $this->data) ||
     !array_key_exists('password', $this->data)) {
      $this->response->setAttributes(false, 400, 'Must have username and password', [] , true)->send();
    }

    $user = new User;
    try {
      $user->setUsername($this->data['username']);
      $user->setPassword($this->data['password']);
    }catch(Exception $e) {
      $this->response->setAttributes(false, 400, $e->getMessage(), [] , true)->send();
    }
    return $user->getUserAsArray();
  }

  public function validateLoginUser() {
    if(!array_key_exists('username', $this->data) || !array_key_exists('password', $this->data)) {
      $this->response->setAttributes(false, 400, 'Login failed', [] , true)->send();
    }

    return array('username' => $this->data['username'], 'password' => $this->data['password']);
  }

  private function keyExist($data) {
    $attribute = [];
    if(array_key_exists('username', $data)) {
      $attribute['username'] = $data['username'];
    }
    if(array_key_exists('password', $data)) {
      $attribute['password'] = $data['password'];
    }
    if(array_key_exists('oldpassword', $data)) {
      $attribute['oldpassword'] = $data['oldpassword'];
    }
    if(array_key_exists('newpassword', $data)) {
      $attribute['newpassword'] = $data['newpassword'];
    }
    return $attribute;
  }
}