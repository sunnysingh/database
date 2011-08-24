Database is a PHP framework for easy database interaction with MySQL.

The purpose of this framework is to take advantage of the MySQLi extension and make queries, prepared statements, and data fetching a whole lot easier.

See for yourself.

```php
<?php

// These values can come from anywhere such as $_GET, and don't have to be escaped
$firstname = "Bob";
$lastname = "Brown";

// Insert data with a prepared statement
$insert = $db->query("INSERT INTO users (firstname, lastname) VALUES(?, ?)", array($firstname, $lastname));

// Truthy value if insert was successful, and false if it failed
if ($insert !== false) {

 // Built-in methods such as fetch_row make retrieving data as simple as...
 $user = $db->fetch_row("SELECT firstname, lastname FROM users WHERE lastname = 'brown'");

 echo $user->firstname;
 echo $user->lastname;

}

?>
```