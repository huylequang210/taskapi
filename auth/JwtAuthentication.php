<?php

namespace Auth;

use Exception;
use \Firebase\JWT\JWT;

class JwtAuthentication {
  protected $key;
  protected $issued_at;
  protected $expiration_time;
  protected $issuer = 'http://task_api.test'; 

  public function __construct() {
    $this->key = $_ENV['JWT_KEY'];
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $this->issued_at = time();
    $this->expiration_time = $this->issued_at + (60 * 60); // valid for 1 hour
  }

  public function login(array $user) {
    if(password_verify($user['input'], $user['password'])) {
      $token = array(
        "iat" => $this->issued_at,
        "exp" => $this->expiration_time,
        "iss" => $this->issuer,
        "data" => array(
          "username" => $user['username'],
        )
      );
      $jwt = JWT::encode($token, $this->key);
      return array('username' => $user['username'], 'jwt' => $jwt);
    }
    return array('message' => 'Wrong password');
  }

  public function authentication(string $jwt) {
    if($jwt) {
      $decoded = JWT::decode($jwt, $this->key, array('HS256'));
      return $decoded;
    }
  }
}