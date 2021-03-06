<?php
declare(strict_types=1);

/*
 * Copyright 2018 by Michael Zapf.
 * Licensed under MIT. See file /LICENSE.
 */

namespace AppBundle\Service\Pool;

class PoolDirectory extends PoolItem
{
    /** @var PoolFile[] */
    protected $contents = [];

    public function __construct(string $filename, string $fullpath)
    {
        parent::__construct($filename, $fullpath);
    }

    /**
     * @return PoolFile[]|array
     */
    public function &getContents(): array
    {
        return $this->contents;
    }

    public function getType(): string
    {
        return PoolItem::TYPE_DIRECTORY;
    }
}
