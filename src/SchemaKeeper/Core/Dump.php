<?php
/**
 * This file is part of the SchemaKeeper package.
 * (c) Dmytro Demchyna <dmitry.demchina@gmail.com>
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SchemaKeeper\Core;

/**
 * @internal
 */
class Dump
{
    /**
     * @var SchemaStructure[]
     */
    private $schemas;

    /**
     * @var string[]
     */
    private $extensions;

    /**
     * @param SchemaStructure[] $schemas
     * @param string[] $extensions
     */
    public function __construct(array $schemas, array $extensions)
    {
        $this->schemas = $schemas;
        $this->extensions = $extensions;
    }

    /**
     * @return SchemaStructure[]
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    /**
     * @return string[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
