# Q.php
A lightweight PHP framework for secure MySQL database access and manipulation.

## Intallation
Q.php is currently only distributed through Composer. To add it to your project, run the following command:

`composer require hazdryx/q.php`

If you don't use composer, you can copy the `/src` folder into your project from the [latest release](https://github.com/hazdryx/Q.php/releases). This project doesn't require any dependencies other than `mysqli extension` and `php 8`.

## Gettings Started
Q.php is a light wrapper for mysqli prepared statements, so knowing the basics of mysqli and prepared statements is highly recommended. Everything you need to know for Q.php will be explained in the examples.

### Q Objects
```php
// Creates a database object.
$db = new mysqli('localhost', 'root', '', 'database');

// Creates Q object which takes in two arguments:
//   1. An SQL statement with ? for parameters.
//   2. An array of parameters to bind to the statement.
$q = new Q('SELECT * FROM books WHERE book_author=?', ['me']);

// Executing requires the database object and returns a QResult.
// This will throw an error if something went wrong unless
// otherwise specified.
$result = $q->execute($db);

// Outputs all the book names from the result->rows, which is
// auto populated with the data.
foreach ($result->rows as $book) {
    echo $book['book_name'] . '<br>';
}
```

### q Function
```php
// Creates a database object.
$db = new mysqli('localhost', 'root', '', 'database');

// The q function creates and executes a Q object behind the
// scenes. It requires the following arguments:
//   1. The database object to call on.
//   2. An SQL statement with ? for parameters.
//   3. An array of parameters to bind to the statement.
//
// This will return a QResult and throw an error if something
// went wrong unless otherwise specified.
$result = q($db, 'SELECT * FROM books WHERE book_author=?', ['me']);

// Outputs all the book names from the result->rows, which is
// auto populated with the data.
foreach ($result->rows as $book) {
    echo $book['book_name'] . '<br>';
}
```
