<?php

namespace Model;
use Db\MysqlConnection;
use Db\PostgresqlConnection;
use Model\Task;
use DateTime;
use PDOException;

class Model {


  protected $db;

  public function __construct() {
    $this->db = (new PostgresqlConnection)->connectDB();
  }

  private function castDateTimeToSql(string $datetime) {
    $date = DateTime::createFromFormat('m-d-Y', $datetime);
    if($date) 
      $date = $date->format('Y-d-m');
    else {
      $date = DateTime::createFromFormat('m-d-Y H:i:s', $datetime);
      if($date) $date = $date->format('Y-m-d H:i:s');
    }
    return $date;
  }

  private function buildPatchQuery(array $data) {
    $prepareStr = '';
    $bindArray = array();
    foreach ($data as $key => $value) {
      $prepareStr .= $key . '=' . '?' . ',';
      $bindArray['value'][] = $value;
    }
    $prepareStr = substr($prepareStr, 0, -1);
    $bindArray['string'] = $prepareStr;
    return $bindArray;
  }

  private function buildInsertQuery(array $data) {
    $prepareStr = '(';
    $prepareStr2 = '(';
    $bindArray = array();
    foreach ($data as $key => $value) {
      $prepareStr .= $key . ',';
      $prepareStr2 .= '?' . ',';
      $bindArray['value'][] = $value;
    }
    $prepareStr = substr($prepareStr, 0, -1);
    $prepareStr2 = substr($prepareStr2, 0, -1);
    $bindArray['string'] = $prepareStr . ') VALUES ' . $prepareStr2 . ')';
    return $bindArray;
  }

  public function fetchAllTasks() {
    $query = $this->db->prepare('SELECT * FROM task ORDER BY id');
    $query->execute();
    return $query->fetchAll();
  }

  public function fetchTaskById($id) {
    $query = $this->db->prepare('SELECT * FROM task WHERE id = :id');
    $query->execute(['id' => $id]);
    $tasks = $query->fetch();
    return $tasks === false ? [] : $tasks;
  }

  public function fetchTasksByComplete($y) {
    $query = $this->db->prepare('SELECT * FROM task WHERE completed = :y');
    $query->execute(['y' => $y]);
    return $query->fetchAll();
  }

  public function insertTaskToDb(array $task) {
    $prepare = $this->buildInsertQuery($task);
    $query = $this->db->prepare('INSERT INTO task' . $prepare['string']);
    $increment = 1;
    foreach($prepare['value'] as $key => $value) {
      $query->bindParam($increment, $prepare['value'][$key]);
      $increment += 1;
    }
    $query->execute();
  }

  public function deleteTask($id) {
    $query = $this->db->prepare('DELETE FROM task WHERE id = :id');
    $query->execute(['id' => $id]);
    return $query->rowCount();
  }
  
  public function patchTaskFromDb(int $id, array $data) {
    $task = $this->fetchTaskById($id);
    if(empty($task)) return false;
    $prepare = $this->buildPatchQuery($data);
    $query = $this->db->prepare('UPDATE task SET' . ' ' . $prepare['string'] . ' ' . 'WHERE id = ?');
    $increment = 1;
    foreach($prepare['value'] as $key => $value) {
      $query->bindParam($increment, $prepare['value'][$key]);
      $increment += 1;
    }
    $query->bindParam(count($prepare['value'])+1, $id);
    $query->execute();
    return true;
  }

  public function createUser(array $data) {
    $query = $this->db->prepare('INSERT INTO customer(username, password) VALUES(:username, :password)');
    $password = $data['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $query->execute(['username' => $data['username'], 'password' => $password_hash]);
  }

  public function findUser(array $data) {
    $query = $this->db->prepare('SELECT id, username, password FROM customer where username = :username');
    $query->execute(['username' => $data['username']]);
    $user = $query->fetch();
    return $user === false ? [] : $user;
  }

  public function deleteUser(array $data) {
    $query = $this->db->prepare('DELETE FROM customer where username = :username');
    $query->execute(['username' => $data['username']]);
  }
}