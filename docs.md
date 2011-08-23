# Setting Up a Connection

For every database that you want to connect to, you will do it like so:

```php
<?php

$db = new Database($name, $host, $username, $password);

?>
```

$name is the database name you're connecting to, $host is the MySQL server host (usually "localhost"), $username is the MySQL username, and password is the MySQL password.