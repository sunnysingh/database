## Setting Up a Connection

For every database that you want to connect to, you will do it like so:

```php
<?php

$db = new Database($name, $host, $username, $password, $charset, $debug, $errormsg);

?>
```

<ul>
 <li>$name (string): Database name you're connecting to.</li>
 <li>$host (string): MySQL server host (usually "localhost")</li>
 <li>$username (string): MySQL username
 <li>$password (string): MySQL password</li>
 <li>$charset (string): MySQL charset. Default set to "utf8".</li>
 <li>$debug (boolean): Turn debug mode on or off. If set to true, error messages will be shown. Errors are logged regardless. Default set to true.</li>
 <li>If $debug is set to false, this message will be shown when there is a connection error. Default set to "Database connection failed.".</li>
</ul>

Note: <code>$db</code> will be used throughout these docs in example code, but you are in no way limited to what you can name your database variable.

## Database::query($query, $params)

Executes a query and returns:

<ul>
 <li>The number of affected rows, if the number is greater than one and if no errors with the query occurred.</li>
 <li>True, if the number of affected rows is zero and if no errors with the query occurred.</li>
 <li>False, if errors with the query occurred.</li>
</ul>

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



