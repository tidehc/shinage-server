<?php
declare(strict_types=1);

/*
 * Copyright 2018 by Michael Zapf.
 * Licensed under MIT. See file /LICENSE.
 */

namespace AppBundle\Service;

class VersionChecker
{
    /**
     * Returns the current installed version.
     */
    public function getVersion(): string
    {
        $exitCode = 0;

        // try to get exact tag
        $r = exec('git describe --tags --abbrev=0', $output, $exitCode);
        if (0 === $exitCode) {
            return $r;
        }

        // otherwise get branch name
        return $this->getBranch() . '#' . $this->getCommit();
    }

    public function getBranch(): string
    {
        $exitCode = 0;
        $branch = exec('git rev-parse --abbrev-ref HEAD', $output, $exitCode);
        if (0 === $exitCode) {
            return $branch;
        }
        return '';
    }

    public function getCommit(): string
    {
        $exitCode = 0;
        $commit = exec('git rev-parse HEAD', $output, $exitCode);
        if (0 === $exitCode) {
            return $commit;
        }
        return '';
    }
}
