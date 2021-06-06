# Q.php
A lightweight PHP framework for secure MySQL database access and manipulation.

## Intallation
Q.php is currently only distributed through Composer. To add it to your project, run the following command:

```
composer require hazdryx/q.php
```

If you don't use composer, you can copy the `/src` folder into your project from the [latest release](https://github.com/hazdryx/Q.php/releases). This project doesn't require any dependencies other than `mysqli extension` and `php 8`.

## Gettings Started
Q.php is a light wrapper for mysqli prepared statements, so knowing the basics of mysqli and prepared statements is highly recommended. Everything you need to know for Q.php will be explained in the examples.

### Q Objects
Q.php is entirely based on `Q` objects. This object contains an SQL string (which can contain `?`) and a parameter array. When you execute a Q object, it creates a prepared statement using a `mysqli` database, the SQL string, and parameters provided and executes that statement.

The result is a `QResult` object, which contains rows, affected rows, error codes, and the `mysqli_stmt` object which was executed.
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
The `q` function serves the same purpose as the `Q` object. It creates an object in the background and executes it for you, returning the `QResult`. The benefit is: it removes some of the boilerplate code if you don't need the Q object for later use (such as calling it on multiple databases).
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

## Contribute
Want to help move this project forward? Consider contributing to the project. There are many different ways you can help out, even if you don't want to submit code changes.

### Use In Your Projects
The easiest way to contribute is to have this repo as a dependency in your projects. This contribution gives the project more recognition and likely to be seen by other developers, thereby growing the community.

### Submit Pull Requests
If you would like to make changes to the codebase or documentation, you can submit a pull request. Make sure to check out the [CONTRIBUTE.md](https://github.com/hazdryx/Q.php/blob/master/CONTRIBUTE.md) for pull request requirements.

### Donate
If you don't want to submit a pull request but still want to support my work further, consider sending me a donation. Donations help me spend more time on open-source projects so that they can be of the highest quality possible. You can send donations using the link(s) below.

[buymeacoffee.com](https://www.buymeacoffee.com/hazdryx)