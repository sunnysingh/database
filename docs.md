# Setting Up a Connection

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

## Database::fetch_field($query, $params)

Fetches a single field and returns it by itself (not in an object or array).

```php
<?php

$age = $db->fetch_field("SELECT age FROM people WHERE name = 'bob' LIMIT 1");

echo $age;

?>
```

Any sensitive variables in the query must be replaced with ? (question marks, a.k.a. markers), and be given as an array in the second argument.

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




