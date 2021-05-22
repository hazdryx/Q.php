<?php
    // 
    // THIS FILE CONTAINS SHORTHAND FUNCTIONS WHICH ENHANCE THE LIBRARY.
    //
    namespace Hazdryx\Q;
    use mysqli;

    /**
     * Creates and executes a Q object.
     * 
     * @author Christopher Bishop
     * @param mysqli - The database to call the query on.
     * @param string $query - An sql query with parameters.
     * @param array $params - An array of parameters (default: []).
     * @param bool $throwError - Whether to throw an error when it fails (default: true).
     * @return QResult the result of the Q executing.
     */
    function q(mysqli $db, string $query, array $params = [], bool $throwError = true): QResult {
        $q = new Q($query, $params);
        return $q->execute($db);
    }
?>