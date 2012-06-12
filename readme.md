Database is a PHP framework for database interaction with MySQL.

The purpose of this framework is to take advantage of the MySQLi extension and to help make queries, prepared statements, and data fetching a whole lot easier.

See for yourself.

```php
<?php

// These values can come from anywhere such as $_GET, and don't have to be escaped
$firstname = "Bob";
$lastname = "Brown";

// Insert data with a prepared statement
$insert = $db->query("INSERT INTO users (firstname, lastname) VALUES(?, ?)", array($firstname, $lastname));

// Truthy value if insert was successful, and false if it failed
if ($insert) {

 // Built-in methods such as fetch_row make retrieving data as simple as...
 $user = $db->fetch_row("SELECT firstname, lastname FROM users WHERE lastname = 'Brown'");

 echo $user->firstname;
 echo $user->lastname;

}
```

## Changelog

### Version 1.0.1 (June 11, 2012)

* The Database::fetch_rows() method works as expected when `$object` argument is set to false.
* Queries in error messages are now wrapped in `<pre>`. This basically allows queries to be displayed with line breaks and such.
* An extra error check has been added after the query executes. Should help with debugging certain errors.