<?php declare(strict_types=1);
    use Hazdryx\Q\Q;
    use PHPUnit\Framework\TestCase;

    final class QTest extends TestCase {
        public function testCreateEmptyQ(): void {
            $q = new Q();
            $this->assertEmpty($q->getQuery());
            $this->assertEmpty($q->getParameters());
        }
        
        /**
         * @dataProvider appendProvider
         */
        public function testAppend(string $query, array $params): void {
            $q = new Q();
            $q->append($query, $params);
            $this->assertEquals($query, $q->getQuery());
            $this->assertEquals($params, $q->getParameters());
        }
        /**
         * @dataProvider appendProvider
         */
        public function testAppendHasCorrectTypeString(string $query, array $params, string $types): void {
            $q = new Q();
            $q->append($query, $params);

            $this->assertEquals($types, $q->getTypeString());
        }
        /**
         * @dataProvider appendProvider
         */
        public function testAppendHasCorrectArgs(string $query, array $params, string $types): void {
            $q = new Q();
            $q->append($query, $params);
            
            $args = [$types];
            for ($i = 0; $i < count($params); $i++) {
                $args[] = &$params[$i];
            }

            $this->assertEquals($args, $q->getArgs());
        }
        public function appendProvider(): array {
            return [
                'no-params' => ['SELECT * FROM users;', [], ''],
                'params' => ['SELECT * FROM users WHERE user_id=? AND user_online=?', [2, true], 'ii'],
                'no-query' => ['', [5.2, false, 'hello'], 'dis']
            ];
        }

        public function testCanMultiAppend(): void {
            $appends = [
                ['INSERT INTO users (user_id, user_name) VALUES (?, ?);', [1, 'Demo']],
                ['SELECT * FROM users WHERE user_id=?;', [1]]
            ];

            $q = new Q();
            foreach ($appends as $append) {
                $q->append($append[0], $append[1]);
            }

            $this->assertEquals('INSERT INTO users (user_id, user_name) VALUES (?, ?);SELECT * FROM users WHERE user_id=?;', $q->getQuery());
            $this->assertEquals([1, 'Demo', 1], $q->getParameters());
        }
    }
?>