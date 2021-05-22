<?php
    namespace Hazdryx\Q;
    use mysqli, mysqli_stmt, ErrorException;

    /**
     * The result from a Q execution.
     * 
     * @author Christopher Bishop
     */
    final class QResult {
        /** @var mysqli_stmt $stmt - the statement executed to get this result. */
        public mysqli_stmt $stmt;

        /** @var array $rows - The rows extracted from the result set. */
        public array $rows = [];

        /** @var int $affectedRows - How many rows were affected.*/
        public int $affectedRows = 0;

        /** @var int $errno - The error number which given by the mysql database. */
        public int $errno = 0;

        /** @var string $error - The error given by the mysql database. */
        public string $error = '';

        private function __construct() { }

        /**
         * @return int the number of rows.
         */
        public function count(): int {
            return count($this->rows);
        }

        /**
         * If there is an error, it throws it as an exception.
         */
        public function throwError() {
            if ($this->errno != 0) {
                throw new ErrorException($this->error, $this->errno);
            }
        }

        /**
         * Builds a query result from a executed statement and it's connection.
         * 
         * @param mysqli $conn - A connection to the MySQL server.
         * @param mysqli_stmt|bool $stmt - A MySQL statement which was just executed.
         * @return QResult - Q result created from the mysqli_stmt.
         */
        public static function fromStmt(mysqli $conn, mysqli_stmt|bool $stmt = false): QResult {
            $result = new QResult();
            $result->stmt = $stmt;
            $result->errno = $conn->errno;
            $result->error = $conn->error;

            if($stmt) {
                $result->affectedRows = $stmt->affected_rows;
                if($stmtResult = $stmt->get_result()) {
                    while($row = $stmtResult->fetch_assoc()) {
                        $result->rows[] = $row;
                    }
                }
            }
            return $result;
        }
    }
?>