<?php

/**
 * MySQL Database Framework for MySQLi
 *
 * Version: 1.0.1 (June 11, 2012)
 * Author: Sunny Singh (http://sunnyis.me)
 * Project Page: http://github.com/sunnysingh/database
 */

class Database {

 public $connection, $connection_error, $connection_error_code, $server_info, $client_info, $host_info, $insert_id, $affected_rows;
 public $query_count_all = 0, $query_count_success = 0, $query_count_error = 0;
 private $debug, $stmt, $result, $filters = array();

 // Sets up database connection
 public function __construct($name, $server, $username, $password, $charset = "utf8", $debug = true, $errormsg = "Database connection failed.") {
  if ($this->debug) { mysqli_report(MYSQLI_REPORT_ERROR); }
  else { mysqli_report(MYSQLI_REPORT_OFF); }
  $this->connection = @new mysqli($server, $username, $password, $name);
  $this->connection_error = $this->connection->connect_error;
  $this->connection_error_code = $this->connection->connect_errno;
  $this->debug = $debug;
  if (!$this->connection_error_code) {
   $this->connection->set_charset($charset);
   if ($charset == "utf8") { $this->connection->query("SET NAMES utf8"); }
   $this->server_info = $this->connection->server_info;
   $this->client_info = $this->connection->client_info;
   $this->host_info = $this->connection->host_info;
  }
  else if ($this->connection_error_code && $errormesg !== false) {
   error_log("MySQL database error:  ".$this->connection_error." for error code ".$this->connection_error_code);
   if ($this->debug) { die("Database Connection Error ".$this->connection_error_code.": ".$this->connection_error); } else { die($errormsg); }
  }
 }

 // Automatically close database connection
 public function __destruct() {
  if (!$this->connection_error_code) { $this->connection->close(); }
 }

  // Filters

 public function add_filter($type, $filter) {
  if (is_callable($filter)) { $this->filters[$type] = $filter; }
  else { $this->filters[$type] = false; }
 }

 public function filter_exists($type) {
  return $this->filters[$type] ? true : false;
 }

 private function apply_filter($type, $args = array()) {
  $call = call_user_func($this->filters[$type], $args);
  return $call[0];
 }

  // Queries

 // Used internally for all queries and externally for INSERT, UPDATE, DELETE, etc.
 public function query($query, $params = array()) {
  if ($this->filter_exists("query")) { $query = $this->apply_filter("query", array($query)); }
  $this->stmt = $this->connection->prepare($query);
  $this->query_count_all++;
  if ($this->stmt) {
   $this->query_count_success++;
   if (count($params)) {
    foreach ($params as $key => $param) {
     if (is_int($param) || is_bool($param)) {
      $markers .= "i";
      $param_trueval = intval($param);
      $params_bindable[] = $param_trueval;
     } else if (is_double($param)) {
      $markers .= "d";
      $param_trueval = doubleval($param);
      $params_bindable[] = $param_trueval;
     } else {
      $markers .= "s";
      $param_trueval = strval($param);
      $params_bindable[] = $param_trueval;
     }
    }
    // For some reason, creating references within the first loop breaks some queries
    foreach ($params_bindable as $key => &$param) {
     $params_bindable_withref[$key] = &$param;
    }
    array_unshift($params_bindable_withref, $markers);
    call_user_func_array(array($this->stmt, "bind_param"), $params_bindable_withref);
   }
   $execute = $this->stmt->execute();
   // An extra check to see if the query executed without errors (first check is when we first prepare the query)
   if (!$execute) {
	$debug_backtrace = debug_backtrace();
	error_log("MySQL database error:  ".$this->stmt->error." for query ".$query." in ".$debug_backtrace[1]["file"]." on line ".$debug_backtrace[1]["line"]);
    if ($this->debug) {
     echo "MySQL database error: ".$this->stmt->error." for query <pre><code>".$query."</code></pre> in ".$debug_backtrace[1]["file"]." on line ".$debug_backtrace[1]["line"];
     exit();
	}
	return false;
   }
   if ($this->stmt->field_count) {
    $fields = $this->stmt->result_metadata()->fetch_fields();
    foreach ($fields as $key => $field) {
     $fields_names[$field->name] = &$field->name;
    }
    call_user_func_array(array($this->stmt, "bind_result"), $fields_names);
    return $fields_names;
   } else {
    // Set or update info relating to the latest query
    $this->insert_id = $this->connection->insert_id;
    $this->affected_rows = $this->connection->affected_rows;
    // Close statement
    $this->stmt->close();
    // Return affected rows if any, otherwise return true (because 0 is considered false)
    if ($this->affected_rows) { return $this->affected_rows; } else { return true; }
   }
  } else {
   $this->query_count_error++;
   $debug_backtrace = debug_backtrace();
   error_log("MySQL database error:  ".$this->connection->error." for query ".$query." in ".$debug_backtrace[1]["file"]." on line ".$debug_backtrace[1]["line"]);
   if ($this->debug) {
    echo "Database error: ".$this->connection->error." for query <pre><code>".$query."</code></pre> in ".$debug_backtrace[1]["file"]." on line ".$debug_backtrace[1]["line"];
    exit();
   }
   return false;
  }
 }

  // Data Fetching

 // Fetch a single field from a single row
 public function fetch_field($query, $params = array()) {
  $this->result = $this->query($query, $params);
  if ($this->result && count($this->stmt->fetch())) {
   $result_value = current($this->result);
   $this->stmt->free_result();
   $this->stmt->close();
   return $result_value;
  } else {
   $this->stmt->free_result();
   $this->stmt->close();
   return false;
  }
 }

 // Fetch multiple fields from a single row
 public function fetch_row($query, $object = true, $params = array()) {
  $this->result = $this->query($query, $params);
  if ($this->result && $this->stmt->store_result() && $this->stmt->num_rows) {
   $this->stmt->fetch();
   if ($object) {
    $result_object = new stdClass();
    foreach ($this->result as $key => $value) {
     $result_object->$key = $value;
    }
    $this->stmt->free_result();
    $this->stmt->close();
    return $result_object;
   } else {
    foreach ($this->result as $key => $value) {
     $result_array[$key] = $value;
    }
    $this->stmt->free_result();
    $this->stmt->close();
    return $result_array;
   }
  } else {
   $this->stmt->close();
   return false;
  }
 }

 // Fetch multiple fields from multiple rows
 public function fetch_rows($query, $object = true, $params = array()) {
  $this->result = $this->query($query, $params);
  if ($this->result && $this->stmt->store_result() && $this->stmt->num_rows) {
   if ($object) {
    while ($this->stmt->fetch()) {
     $row = new stdClass();
     foreach ($this->result as $key => $value) {
      $row->$key = $value;
     }
     $result_object[] = $row;
    }
    $this->stmt->free_result();
    $this->stmt->close();
    return $result_object;
   } else {
    while ($this->stmt->fetch()) {
     $row = array();
     foreach ($this->result as $key => $value) {
      $row[$key] = $value;
     }
     $result_array[] = $row;
    }
    $this->stmt->free_result();
    $this->stmt->close();
    return $result_array;
   }
  } else {
   $this->stmt->close();
   return false;
  }
 }

 // Deprecated, use the parameter binding feature to escape user data
 public function escape($str) {
  return $this->connection->real_escape_string($str);
 }

}

?>