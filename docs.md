# Documentation

This is where you will learn the ins and outs of this framework.

## Setting Up a Connection

For every database that you want to connect to, you will set it up like so.

```php
<?php

$db = new Database($name, $host, $username, $password, $charset, $debug, $errormsg);

?>
```

* $name (string): Database name you're connecting to
* $host (string): MySQL server host (usually "localhost")
* $username (string): MySQL username
* $password (string): MySQL password
* $charset (string): MySQL charset. Default set to "utf8"
* $debug (boolean): Turn debug mode on or off. If set to true, error messages will be shown. Errors are logged regardless. Default set to true
* If $debug is set to false, this message will be shown when there is a connection error. Default set to "Database connection failed."

Note: <code>$db</code> will be used throughout these docs in example code, but you are in no way limited to what you can name your database variable.

## Database::query($query, $params)

Executes a query and returns:

* The number of affected rows, if the number is greater than one and if no errors with the query occurred.
* True, if the number of affected rows is zero and if no errors with the query occurred.
* False, if errors with the query occurred.

You would typically use this method for an INSERT, UPDATE, DELETE, or similar queries.

```php
<?php

$insert = $db->query("INSERT INTO people (name, age) VALUES('Bob', '123')");

if ($insert !== false) {

 // insert was successful
 // checking that $insert is not false will guarantee that this code will run only when no errors have occurred

}

?>
```

Any sensitive variables in the query must be replaced with ? (question marks, a.k.a. markers), and be given as an array in the second $params argument.

```php
<?php

$name = $_GET["name"];
$age = $_GET["age"];

$insert = $db->query("INSERT INTO people (name, age) VALUES(?, ?)", array($name, $age));

?>
```

## Database::fetch_field($query, $params)

Fetches a single field and returns it by itself (not in an object or array).

```php
<?php

$age = $db->fetch_field("SELECT age FROM people WHERE name = 'bob' LIMIT 1");

echo $age;

?>
```

Any sensitive variables in the query must be replaced with ? (question marks, a.k.a. markers), and be given as an array in the second $params argument.

```php
<?php

// the WRONG way

$name = $_GET["name"];

$age = $db->fetch_field("SELECT age FROM people WHERE name = '$name' LIMIT 1");

echo $age;

// the RIGHT way

$name = $_GET["name"];

$age = $db->fetch_field("SELECT age FROM people WHERE name = ? LIMIT 1", array($name));

echo $age;

?>
```

## Database::fetch_row($query, $object, $params)

Fetches fields from a single row and returns them as an object or array.


```php
<?php

$person = $db->fetch_row("SELECT name, age FROM people LIMIT 1");

echo $person->name;
echo $person->age;

?>
```

The second $object argument can be set to false to return an array instead.

```php
<?php

$person = $db->fetch_row("SELECT name, age FROM people LIMIT 1", false);

echo $person["name"];
echo $person["age"];

?>
```

Any sensitive variables in the query must be replaced with ? (question marks, a.k.a. markers), and be given as an array in the third $params argument.

```php
<?php

// the WRONG way

$age = $_GET["age"];
$age2 = $_GET["age2"];

$person = $db->fetch_row("SELECT name, age FROM people WHERE age = '$age' OR age = '$age2' LIMIT 1");

echo $person->name;
echo $person->age;

// the RIGHT way

$age = $_GET["age"];
$age2 = $_GET["age2"];

$person = $db->fetch_row("SELECT name, age FROM people WHERE age = ? OR age = ? LIMIT 1", true, array($age, $age2));

echo $person->name;
echo $person->age;

?>
```

## Database::fetch_rows($query, $object, $params)

Fetches fields from multiple rows and returns them as objects or arrays contained one array.


```php
<?php

$people = $db->fetch_rows("SELECT name, age FROM people");

foreach ($people as $person) {
 echo $person->name;
 echo $person->age;
}

?>
```

The second $object argument can be set to false to return each row as an array instead.

```php
<?php

$people = $db->fetch_rows("SELECT name, age FROM people", false);

foreach ($people as $person) {
 echo $person["name"];
 echo $person["age"];
}

?>
```

Any sensitive variables in the query must be replaced with ? (question marks, a.k.a. markers), and be given as an array in the third $params argument.

```php
<?php

// the WRONG way

$age = $_GET["age"];
$age2 = $_GET["age2"];

$people = $db->fetch_rows("SELECT name, age FROM people WHERE age = '$age' OR age = '$age2'");

foreach ($people as $person) {
 echo $person->name;
 echo $person->age;
}

// the RIGHT way

$age = $_GET["age"];
$age2 = $_GET["age2"];

$people = $db->fetch_rows("SELECT name, age FROM people WHERE age = ? OR age = ?", true, array($age, $age2));

foreach ($people as $person) {
 echo $person->name;
 echo $person->age;
}

?>
```

## Filters

There is currently one filter that allows you to modify your query before it gets executed.
Some use cases for this is automatically adding database or table prefixes. This is particularly useful on shared hosts where the names of databases are prefixed with your username.

```php
<?php

$db->add_filter("query", function($query) {

 return str_replace("my_custom_prefix_", "actual_username_prefix_", $query);

});

?>
```

This way you can do queries like <code>SELECT my_custom_prefix_accounts.users</code> and your query filter will be run before the actual query is executed.
With a more complex "str_replace" or even with some regex, you can eliminate prefixes all together.

Filters are of course optional and were created due to my experience with shared hosts and database prefixes.

## API

There are some public variables that allow you to retrieve important info.

* $db->connection is a hook to the actual MySQLi class. It is used internally and should only be used internally, but if for some reason you find the need to use it go ahead.
* $db->connection_error contains the error message (if any) during a connection failure.
* $db->connection_error_code contains the error code (if any) during a connection failure.

* $db->server_info, $db->client_info, and $db->host_info are exactly the same as the MySQLi counterparts.

* $db->insert_id contains the auto_increment generated ID frpm the last INSERT query
* $db->affected_rows contains the number of affected rows from the last row-modifying query

* $db->query_count_all contains the total number of executed queries
* $db->query_count_success contains the total number of <strong>successful</strong> queries
* $db->query_count_error contains the total number of <strong>failed</strong> queries

## Debugging

As stated under the "Setting Up a Connection" section, all errors are logged and you will see error messages in your browser if you set the $debug argument to true.

Extra debugging information is given when errors occur, including line numbers and the actual query that was executed.
It is recommended to turn debug mode off on production sites so that such information is not shown to the public.
It is however made sure that the database password is never shown to the user.

## Known Issues

* You cannot do wildcard SELECT queries such as <code>SELECT * FROM...</code> due to the nature of how this framework operates. This shouldn't be a huge issue for anyone since it is good practice to ist out all the columns that you want to select anyway.
* Make sure to have the same amount of ? (markers) as parameters (the last argument in the query and fetch methods). This isn't an issue but is simply how prepared statements work.
* There might be a couple others that I'm either forgetting or haven't come across yet. I use this framework myself so I try to keep the number of known issues and bugs low to none. Please report any issues you come across.
