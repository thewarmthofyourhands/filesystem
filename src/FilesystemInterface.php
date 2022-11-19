<?php

declare(strict_types=1);

namespace Eva\Filesystem;

interface FilesystemInterface
{
    public function mkdir(string $path, int $permission, bool $recursive, mixed $context): void;
    public function rm(string $path, bool $recursive): bool;
    public function cp(string $from, string $to): void;
    public function mv(string $from, string $to): void;
    public function ls(string $path): array;
    public function copyDirectory(string $from, string $to): void;
}
