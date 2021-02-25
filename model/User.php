<?php

namespace Model;
use Exception;

class User {
  private $_username;
  private $_password;
  const MAX_NAME_LEN = 16;
  const MIN_NAME_LEN = 3;
  const MIN_PASSWORD_LEN = 6;
  const MAX_PASSWORD_LEN = 16;

  public function getUsername() {
    return $this->_username;
  }

  public function getPassword() {
    return $this->_password;
  }

  public function setUsername(string $username) {
    if(strlen($username) < self::MIN_NAME_LEN || strlen($username) > self::MAX_NAME_LEN) {
      throw new Exception("username length must be betwwen " . self::MIN_NAME_LEN . ' and ' . self::MAX_NAME_LEN);
    }
    $this->_username = $username;
  }

  public function setPassword(string $password) {
    if(strlen($password) < self::MIN_PASSWORD_LEN || strlen($password) > self::MAX_PASSWORD_LEN) {
      throw new Exception("password length must be betwwen " . self::MIN_PASSWORD_LEN . ' and ' . self::MAX_PASSWORD_LEN);
    }
    $this->_password = $password;
  }

  public function getUserAsArray() {
    $user = array();
    $user['username'] = $this->getUsername();
    $user['password'] = $this->getPassword();
    return $user;
  }
}