<?php
    namespace Hazdryx;

    /**
     * A class which can create and execute query statements on different databases and return
     * results which are already processed.
     * 
     * @version v1.0.0
     * @author Christopher Bishop
     */
    class Q {
        private static ReflectionMethod $bindMethod;
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
        public function getArgs(): string {
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
        private function getTypeChar($obj): string {
            $type = gettype($obj);
            if($type == 'object') $class = get_class($obj);

            if($type == 'string') return 's';
            else if($type == 'boolean' || $type == 'integer') return 'i';
            else if($type == 'double') return 'd';
            else if($type == 'object' && ($class == 'Closure' || $class == 'Callable')) return false;
            else return 'b';
        }
    }
?>