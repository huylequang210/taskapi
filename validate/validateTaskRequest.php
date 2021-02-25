<?php

namespace Validate;

use Model\Task;
use Model\Response;
use Exception;

class validateTaskRequest {
  protected $data = [];
  protected $response;
  protected $httpMethod;

  public function __construct($data, $httpMethod) {
    $this->data = $data;
    $this->httpMethod = $httpMethod;
    $this->response = new Response;
    $this->validateFunc();
  }

  public function validateFunc() {
    if($this->httpMethod === "POST") {
      return $this->validatePost();
    }
    if($this->httpMethod === "PATCH") {
      return $this->validatePatch();
    } 
  }
 
  public function validatePost() {
    if(!array_key_exists('title', $this->data)) {
      $this->response->setAttributes(false, 400, 'Must have title', [] , true)->send();
    }
    $attributes = $this->keyExist($this->data);

    $task = new Task();
    foreach ($attributes as $key => $value) {
      try {
        $task->setAttribute($key, $value);
      } catch(Exception $e) {
        $this->response->setAttributes(false, 400, $e->getMessage(), [] , true)->send();
      }
      $patchArr[$key] = $task->getAttribute($key);
    }
    return $patchArr;
  }

  public function validatePatch() {
    $attributes = $this->keyExist($this->data);
    if(empty($attributes)) {
      $this->response->setAttributes(false, 400, 'Must have a attribute', [] , true)->send();
    }
    $patchArr = [];
    $task = new Task();
    foreach ($attributes as $key => $value) {
      try {
        $task->setAttribute($key, $value);
      } catch(Exception $e) {
        $this->response->setAttributes(false, 400, $e->getMessage(), [] , true)->send();
      }
      $patchArr[$key] = $task->getAttribute($key);
    }
    return $patchArr;
  }

  private function keyExist($data) {
    $attribute = [];
    if(array_key_exists('title', $data)) {
      $attribute['title'] = $data['title'];
    }
    if(array_key_exists('description', $data)) {
      $attribute['description'] = $data['description'];
    }
    if(array_key_exists('deadline', $data)) {
      $attribute['deadline'] = $data['deadline'];
    }
    if(array_key_exists('completed', $data)) {
      $attribute['completed'] = $data['completed'];
    }
    return $attribute;
  }
}