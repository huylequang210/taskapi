<?php

namespace Model;

use DateTime;
use Exception;
use Model\Response;

class Task {
  const MAX_TITLE_LEN = 100;
  const MAX_DESCRIPTION_LEN = 300;
  const INPUT_DATE_FORMAT = ['m-d-Y', 'm-d-Y H:i:s', 'Y-m-d', 'Y-m-d H:i:s'];

  private $_id;
  private $_title;
  private $_description;
  private $_deadline;
  private $_completed;

  public function getID() {
    return $this->_id;
  }

  public function getTitle() {
    return $this->_title;
  }

  public function getDescription() {
    return $this->_description;
  }

  public function getDeadline() {
    return $this->_deadline;
  }

  public function getCompleted() {
    return $this->_completed;
  }

  public function setAttribute(string $attribute, $value) {
    if(!$value) $value = '';
    if($attribute === 'title') {
      $this->setTitle($value);
    }
    if($attribute === 'description') {
      $this->setDescription($value);
    }
    if($attribute === 'deadline') {
      $this->setDeadLine($value);
    }
    if($attribute === 'completed') {
      $this->setCompleted($value);
    }
  }

  public function getAttribute(string $attribute) {
    if($attribute === 'title') {
      return $this->getTitle($attribute);
    }
    if($attribute === 'description') {
      return $this->getDescription($attribute);
    }
    if($attribute === 'deadline') {
      return $this->getDeadline($attribute);
    }
    if($attribute === 'completed') {
      return $this->getCompleted($attribute);
    }
  }

  public function setAttributes(array $attributes) {
    $title = $attributes['title'] ?? '';
    $description = $attributes['description'] ?? '';
    $deadline = $attributes['deadline'] ?? '';
    $completed = $attributes['completed'] ?? '';

    $this->setTitle($title);
    $this->setDescription($description);
    $this->setDeadLine($deadline);
    $this->setCompleted($completed);
  }

  public function setTitle(string $title) {
    if(strlen($title) > self::MAX_TITLE_LEN || strlen($title) <= 0) {
      //(new Response)->setAttributes(true, 400, 'Confirm user', [], false)->send();
      throw new Exception("Task title must be no longer " . self::MAX_TITLE_LEN . " character" . ' or empty');
    }
    $this->_title = $title;
  }

  public function setDescription(string $description) {
    if(strlen($description) > self::MAX_DESCRIPTION_LEN) {
      throw new Exception("Task description must be no longer " . self::MAX_DESCRIPTION_LEN . " character");
    }
    if($description === '') $description = null;
    $this->_description = $description;
  }

  public function setCompleted(string $completed) {
    if(strtoupper($completed) !== 'Y' && strtoupper($completed) !== 'N') {
      throw new Exception("Task completed must be Y or N");
    }
    if($completed === '') $completed = null;
    $this->_completed = $completed;
  }

  public function setDeadLine(string $deadline) {
    if(empty($deadline) || $deadline === "") {
      $this->_deadline = null;
      return;
    }

    $selectedFormat = null;
    foreach (self::INPUT_DATE_FORMAT as $format) {
      $date = DateTime::createFromFormat($format, $deadline);
      $selectedFormat = $format;
      if($date) {
        break;
      }
    }
    if($date === false) 
      throw new Exception('Wrong datetime format. (eg mm-dd-yyyy, mm-dd-yyyy hh-ii-ss)');
    if(strpos($selectedFormat, 'H:i:s')) 
      $date = $date->format('Y-m-d H:i:s');
    else 
      $date = $date->format('Y-m-d');

    $this->_deadline = $date;
  }


  public function getTaskAsArray() {
    $task = array();
    $task['title'] = $this->getTitle();
    $task['description'] = $this->getDescription();
    $task['deadline'] = $this->getDeadline();
    $task['completed'] = $this->getCompleted();
    return $task;
  }
}