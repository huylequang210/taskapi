<?php

namespace Model;

class Response {
  private $_success;
  private $_httpStatusCode;
  private $_messages = '';
  private $_data;
  private $_toCache = false;
  private $_responseData = array();

  public function setSuccess(bool $success) {
    $this->_success = $success;
  }

  public function setHttpStatusCode(int $httpStatusCode) {
    $this->_httpStatusCode = $httpStatusCode;
  }

  public function addMessage(string $messages) {
    $this->_messages = $messages;
  }

  public function setData(array $data = []) {
    $this->_data = $data;
  }

  public function toCache(bool $toCache) {
    $this->_toCache = $toCache;
  }

  public function setAttributes($success, $httpStatusCode, $messages, $data, $toCache) {
    $this->setSuccess($success);
    $this->setHttpStatusCode($httpStatusCode);
    $this->addMessage($messages);
    $this->setData($data);
    $this->toCache($toCache);

    return $this;
  }
  
  public function send() {
    header('Content-type: application/json;charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    Header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header("Access-Control-Max-Age: 3600");    
    header("Access-Control-Allow-Headers: Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    if($this->_toCache == false) {
      header('Cache-control: no-cache, no-store');
    }
    header('Cache-control: max-age=60');

    if(($this->_success !== false && $this->_success !== true) || !is_numeric($this->_httpStatusCode)) {
      http_response_code(500);
      $this->_responseData['statusCode'] = 500;
      $this->_responseData['success'] = false;
      $this->addMessage("Response creation error");
      $this->_responseData['messages'] = $this->_messages;
    }
    http_response_code($this->_httpStatusCode);
    $this->_responseData['statusCode'] = $this->_httpStatusCode;
    $this->_responseData['success'] = $this->_success;
    $this->_responseData['messages'] = $this->_messages;
    $this->_responseData['data'] = $this->_data;
    echo json_encode($this->_responseData);
    exit;
  }
  
}