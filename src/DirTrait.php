<?php

declare(strict_types=1);

namespace Eva\Filesystem;

use Directory;
use Generator;

trait DirTrait
{
    public function isDir(string $path): bool
    {
        return is_dir($path);
    }

    public function dir(string $path, mixed $context = null): Directory|false
    {
        return dir($path, $context);
    }

    public function scanDir(Directory $dir): Generator
    {
        while ($read = $dir->read()) {
            yield $read;
        }
    }

    public function getcwd(): string|false
    {
        return getcwd();
    }

    public function chdir(string $directory): bool
    {
        return chdir($directory);
    }

    public function chroot(string $directory): bool
    {
        return chroot($directory);
    }
}
