# Documentation

Learn how to <del>fly</del> <ins>use the framework</ins>.

## Setting Up a Connection

For every database that you want to connect to, you will set it up like so.

```php
<?php

$db = new Database($name, $hostname, $username, $password, $charset, $debug, $errormsg);
```

* `$name` (string): Database name you're connecting to.
* `$hostname` (string): MySQL server hostname (usually "localhost").
* `$username` (string): MySQL username.
* `$password` (string): MySQL password.
* `$charset` (string): MySQL charset. Default set to "utf8."
* `$debug` (boolean): Turn debug mode on or off. If set to true, error messages will be shown. Errors are logged regardless. Default set to true.
* If `$debug` is set to false, this message will be shown when there is a connection error. Default set to "Database connection failed."

**Note**: The `$db` variable will be used throughout code snippets, but you are in no way limited to what you can name your database variable(s).

```php
<?php

// Example of two basic database connections

$accounts_database = new Database("accounts", "localhost", "root", "");
$music_database = new Database("music", "localhost", "root", "");
```

## Database::query($query, $params)

Executes a query and returns:

* The number of affected rows if the number is greater than one and if the query contains **no** errors.
* True if the number of affected rows is zero and if the query contains **no** errors.
* False if the query contains errors.

You would typically use this method for an INSERT, UPDATE, DELETE, or similar query.

```php
<?php

$insert = $db->query("INSERT INTO people (name, age) VALUES('Bob', '123')");

if ($insert) {

 // Insert was successful
 // You can also explicitly check if $insert returned true or false, but a simple check like this is usually enough

}
```

Any sensitive variables in the query must be replaced with ? (question marks, a.k.a. markers), and be given as an array in the second $params argument.

```php
<?php

$name = $_GET["name"];
$age = $_GET["age"];

$insert = $db->query("INSERT INTO people (name, age) VALUES(?, ?)", array($name, $age));
```

## Database::fetch_field($query, $params)

Fetches a single field and returns it by itself (not in an object or array).

```php
<?php

$name = $db->fetch_field("SELECT name FROM people WHERE age = '123' LIMIT 1");

echo $name;
```

Any sensitive variables in the query must be replaced with ? (question marks, a.k.a. markers), and be given as an array in the second $params argument.

```php
<?php

// the WRONG way

$age = $_GET["age"];

$name = $db->fetch_field("SELECT name FROM people WHERE age = '$age' LIMIT 1");

echo $name;

// the RIGHT way

$age = $_GET["age"];

$name = $db->fetch_field("SELECT name FROM people WHERE age = ? LIMIT 1", array($age));

echo $name;
```

## Database::fetch_row($query, $object, $params)

Fetches fields from a single row and returns them as an object or array.


```php
<?php

$person = $db->fetch_row("SELECT name, age FROM people LIMIT 1");

echo $person->name;
echo $person->age;
```

The second $object argument can be set to false to return an array instead.

```php
<?php

$person = $db->fetch_row("SELECT name, age FROM people LIMIT 1", false);

echo $person["name"];
echo $person["age"];
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
```

## Database::fetch_rows($query, $object, $params)

Fetches fields from multiple rows and returns them in an array as objects or arrays.


```php
<?php

$people = $db->fetch_rows("SELECT name, age FROM people");

foreach ($people as $person) {
 echo $person->name;
 echo $person->age;
}
```

The second $object argument can be set to false to return each row as an array instead.

```php
<?php

$people = $db->fetch_rows("SELECT name, age FROM people", false);

foreach ($people as $person) {
 echo $person["name"];
 echo $person["age"];
}
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
```

## Filters

There is currently one filter that allows you to modify your query before it gets executed.
A use case for this is automatically removing database prefixes. This is particularly useful on shared hosts where the names of databases are prefixed with your username.

```php
<?php

$db->add_filter("query", function($query) {

 return str_replace(

  array("accounts.", "music."),
  array("bob_accounts.", "bob_music."),

 $query);

});
```

This way you can write nicer looking queries like <code>SELECT music.albums</code> instead of <code>SELECT bob_music.albums</code>.
Filters are of course optional and were created due to my experience with shared hosts and database prefixes.

## API

There are some public variables that allow you to retrieve important info.

* <code>$db->connection</code> <p>A hook to the actual MySQLi class. It is used internally and should only be used when a feature is not part of the framework but is part of the MySQLi class.</p>
* <code>$db->connection_error</code> <p>Contains the error message (if any) during a connection failure.</p>
* <code>$db->connection_error_code</code> <p>Contains the error code (if any) during a connection failure.</p>

* <code>$db->server_info</code>, <code>$db->client_info</code>, <code>$db->host_info</code> <p>Exactly the same as the MySQLi counterparts.</p>

* <code>$db->insert_id</code> <p>Contains the auto_increment generated ID from the last INSERT query</p>
* <code>$db->affected_rows</code> <p>Contains the number of affected rows from the last row-modifying query</p>

* <code>$db->query_count_all</code> <p>Contains the total number of executed queries</p>
* <code>$db->query_count_success</code> <p>Contains the total number of <strong>successful</strong> queries</p>
* <code>$db->query_count_error</code> <p>Contains the total number of <strong>failed</strong> queries</p>

## Debugging

As stated under the "Setting Up a Connection" section, all errors are logged and you will see error messages in your browser if you set the `$debug` argument to true.

Extra debugging information is given when errors occur, including line numbers and the actual query that was executed.
It is recommended to turn debug mode off on production sites so that such information is not shown to the public.

## Known Issues

* You cannot do wildcard SELECT queries such as `SELECT * FROM...` due to the nature of how this framework operates. This shouldn't be a huge issue for anyone since it is good practice to list out all the columns that you want to select anyway.
* Make sure to have the same amount of `?` (markers) as parameters (the last argument in the query and fetch methods). This isn't an issue but is simply how prepared statements work.
* There might be a couple others that I'm either forgetting or haven't come across yet. I use this framework myself so I try to keep the number of known issues and bugs low to none. Please report any issues you come across.
