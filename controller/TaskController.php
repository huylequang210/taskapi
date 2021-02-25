<?php

namespace Controller;

use Model\Model;
use Model\Response;

class TaskController {
  protected $model;
  protected $response;

  public function __construct() {
    $this->model = new Model;
    $this->response = new Response;
  }

  public function tasks() {
    $tasks = $this->model->fetchAllTasks();
    $this->response->setAttributes(true, 200, 'Get all tasks', $tasks, true)->send();
  }

  public function task($vars) {
    $task = $this->model->fetchTaskById($vars['id']);
    $this->response->setAttributes(true, 200, 'Get task id = ' . $vars['id'], $task, true)->send();
  }

  public function tasksComplete() {
    $tasks = $this->model->fetchTasksByComplete('Y');
    $this->response->setAttributes(true, 200, 'Get tasks with completed tag', $tasks, true)->send();
  }

  public function tasksIncomplete() {
    $tasks = $this->model->fetchTasksByComplete('N');
    $this->response->setAttributes(true, 200, 'Get tasks with incompleted tag', $tasks, true)->send();
  }

  public function createTask($vars) {
    $this->model->insertTaskToDb($vars['task']);
    $this->response->setAttributes(true, 200, 'Insert task successfully', $vars, false)->send();
  }

  public function deleteTask($vars) {
    $row = $this->model->deleteTask($vars['id']);
    $this->response->setAttributes(true, 200, 'Deleted ' . $row . ' task(s)', [], false)->send();
  }

  public function patchTask($vars) {
    $result = $this->model->patchTaskFromDb($vars['id'], $vars['task']);
    if($result === false) $this->response->setAttributes(true, 200, 'No task',[], false)->send();
    $this->response->setAttributes(true, 200, 'Patch task successfully',[], false)->send();
  }


} 