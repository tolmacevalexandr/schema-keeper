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
class PSQLChecker
{
    /**
     * @var PSQLParameters
     */
    private $parameters;


    public function __construct(PSQLParameters $parameters)
    {
        $this->parameters = $parameters;
    }

    public function check(): void
    {
        $executable = $this->parameters->getExecutable();
        $exists = false;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //throw new KeeperException('OS Windows is currently not supported');
            $result = shell_exec("if exist $executable echo y");
            $exists = (bool)trim($result);
        } else {
            exec('command -v ' . $executable . ' >/dev/null 2>&1 || exit 1', $output, $retVal);
            $exists = ($retVal === 0);
        }

        if ( ! $exists) {
            throw new KeeperException($executable . ' not installed. Please, install "postgresql-client" package');
        }
    }
}
