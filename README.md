A PHP database framework for use with MySQL databases.

The purpose of this project is to take advantage of the MySQLi extension and makes queries, prepared statements, and fetching data a whole lot easier.

Here's an example:

    <?php

    // These can come from anywhere, such as $_GET and don't have to be escaped
    $firstname = "Bobby";
    $lastname = "Brown";
    $username = "bob";

    // Insert data with a prepared statement (notice the easy syntax)
    $insert = $db->query("INSERT INTO users (firstname, lastname, username) VALUES(?, ?, ?)", array($firstname, $lastname, $username));

    // Truthy value if insert was successful, and false if it failed
    if ($insert !== false) {

     // Built-in methods such as fetch_row make retrieving data as simple as:
     $user = $db->fetch_row("SELECT firstname, lastname FROM users WHERE username = 'bob'");

     echo $user->firstname;
     echo $user->lastname;

    }

    ?>