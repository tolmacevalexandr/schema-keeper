<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Provider\PostgreSQL;

use SchemaKeeper\Exception\KeeperException;

/**
 * @internal
 */
class PSQLClient
{
    /**
     * @var string
     */
    private $executable;

    /**
     * @var string
     */
    protected $dbName;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    public function __construct(string $executable, string $dbName, string $host, int $port, string $user, string $password)
    {
        $this->executable = $executable;
        $this->dbName = $dbName;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
    }

    public function run(string $command): ?string
    {
        $this->putPassword();

        //$req = "echo " . escapeshellarg($command) . " | ".$this->generateScript(); // failed on Windows
        $req = $this->generateScript() . ' -c ' . escapeshellarg($command);

        return shell_exec($req);
    }

    /**
     * @param array<string, string> $commands
     * @return array<string, string>
     * @throws KeeperException
     */
    public function runMultiple(array $commands): array
    {
        $this->putPassword();

        if (!$commands) {
            return [];
        }

        $results = [];

        $max_commands_cnt =  (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 100 : 500;

        if (count($commands) > $max_commands_cnt) {
            $parts = array_chunk($commands, $max_commands_cnt, true);

            foreach ($parts as $part) {
                $results = array_merge($results, $this->runMultiple($part));
            }

            return $results;
        }

        $commandsString = '';
        $separator = '##|$$1$$$$#$$1$$$|##';


        foreach ($commands as $cmd) {
            $commandsString .= ' -c ' . escapeshellarg($cmd) . ' -c ' . escapeshellarg("\qecho -n '" . $separator . "'");
        }

        $req = $this->generateScript() . $commandsString;

        $rawOutput = (string) shell_exec($req);

        $outputs = explode($separator, $rawOutput);

        $i = 0;
        foreach ($commands as $table => $cmd) {
            $results[$table] = $outputs[$i];
            $i++;
        }

        return $results;
    }

    private function generateScript(): string
    {
        return $this->executable . ' -U' . $this->user . ' -h' . $this->host . ' -p' . $this->port . ' -d' . $this->dbName;
    }

    private function putPassword(): void
    {
        putenv("PGPASSWORD=" . $this->password);
    }
}
