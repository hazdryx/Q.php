<?php
    namespace Hazdryx\Q;
    use mysqli, mysqli_stmt, ReflectionClass, ReflectionMethod;

    /**
     * A class which can create and execute query statements on different databases and return
     * results which are already processed.
     * 
     * @author Christopher Bishop
     */
    class Q {
        private static ?ReflectionMethod $bindMethod = null;
        private string $query = '';
        private array $params = [];
        private string $types = '';

        public function __construct(string $query = '', array $params = []) {
            $this->append($query, $params);
        }

        /**
         * @return string the query string.
         */
        public function getQuery(): string { return $this->query; }
        /**
         * @return array the parameters used in the SQL statement
         */
        public function getParameters(): array { return $this->params; }
        /**
         * @return bool whether this query has parameters.
         */
        public function hasParameters(): bool { return count($this->params) > 0; }
        /**
         * @return string all the types for each parameter. Used when binding the parameters to a statement.
         */
        public function getTypeString(): string { return $this->types; }
        /**
         * @return string the array of arguments to pass to the bind_param method.
         */
        public function getArgs(): array {
            $args = [$this->types];
            $params = $this->params;
            for($i = 0; $i < count($params); $i++) {
                $args[] = &$params[$i];
            }
            return $args;
        }

        /**
         * Appends the current query with a new query and parameter pair.
         * 
         * @author Christopher Bishop
         * @param string $query - An sql query with parameters.
         * @param array $params - An array of parameters (optional).
         * @return Q this Q for chaining.
         */
        public function append(string $query, array $params = []): Q {
            for ($i = 0; $i < count($params); $i++) {
                $param = $params[$i];
                if($type = $this->getTypeChar($param)) {
                    $this->types .= $type;
                    $this->params[] = $params[$i];
                }
                else trigger_error("Failed to add parameter $i to the list of parameters.", E_USER_ERROR);
            }
            $this->query .= $query;

            return $this;
        }
        private function getTypeChar(mixed $obj): string {
            $type = gettype($obj);
            if($type == 'object') $class = get_class($obj);

            if($type == 'string') return 's';
            else if($type == 'boolean' || $type == 'integer') return 'i';
            else if($type == 'double') return 'd';
            else if($type == 'object' && ($class == 'Closure' || $class == 'Callable')) return false;
            else return 'b';
        }

        /**
         * Prepares a statement using the database connection.
         * 
         * @param mysqli $conn - Database connection.
         * @return mysqli_stmt|false the unexecuted statement which has the variable bindings.
         */
        public function prepare(mysqli $conn): mysqli_stmt {
            $stmt = $conn->prepare($this->getQuery());
            if ($stmt) {
                if($this->hasParameters()) Q::bindParam($stmt, $this->getArgs());
                return $stmt;
            }
            return false;
        }
        private static function bindParam(mysqli_stmt $stmt, array $args) {
            if (!Q::$bindMethod) {
                $stmtClass = new ReflectionClass('mysqli_stmt');
                Q::$bindMethod = $stmtClass->getMethod('bind_param');
            }
            Q::$bindMethod->invokeArgs($stmt, $args);
        }

        /**
         * Executes a statement using the database connection.
         * 
         * @param mysqli $conn - Database connection.
         * @param bool $closeStmt - Whether or not to close the statement.
         * @param bool $throwError - Whether or not to throw the error as an exception.
         * @return QResult the result from the execution.
         */
        public function execute(mysqli $conn, bool $throwError = true, bool $closeStmt = true): QResult {
            // Prepare statement
            $stmt = $this->prepare($conn);
            $result;

            // Execute and get results.
            if ($stmt) {
                $stmt->execute();
                $result = QResult::fromStmt($conn, $stmt);
                if ($closeStmt) $stmt->close();
            }
            else $result = QResult::fromStmt($conn);

            // Return results.
            if ($throwError) $result->throwError();
            return $result;
        }
    }
?>