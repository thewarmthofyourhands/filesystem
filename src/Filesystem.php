<?php

declare(strict_types=1);

namespace Eva\Filesystem;

use Directory;
use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;

class Filesystem implements FilesystemInterface
{
    use DirTrait;

    public function basename(string $path, string $suffix = ""): string
    {
        return basename($path, $suffix);
    }

    public function chgrp(string $filename, string|int $group): bool
    {
        return chgrp($filename, $group);
    }

    public function chmod(string $filename, int $permissions): bool
    {
        return chmod($filename, $permissions);
    }

    public function chown(string $filename, string|int $user): bool
    {
        return chown($filename, $user);
    }

    public function clearstatcache(bool $clear_realpath_cache = false, string $filename = ""): void
    {
        clearstatcache($clear_realpath_cache, $filename);
    }

    public function copy(string $from, string $to, mixed $context = null): bool
    {
        return copy($from, $to, $context);
    }

    public function dirname(string $path, int $levels = 1): string
    {
        return dirname($path, $levels);
    }

    public function fileExists(string $filename): bool
    {
        return file_exists($filename);
    }

    public function fclose(mixed $stream): bool
    {
        return fclose($stream);
    }

    public function feof(mixed $stream): bool
    {
        return feof($stream);
    }

    public function fflush(mixed $stream): bool
    {
        return fflush($stream);
    }

    public function fgets(mixed $stream, null|int $length = null): string|false
    {
        return fgets($stream, $length);
    }

    public function fopen(
        string $filename,
        string $mode,
        bool $useIncludePath = false,
        mixed $context = null
    ): mixed {
        return fopen(
            $filename,
            $mode,
            $useIncludePath,
            $context,
        );
    }

    public function fread(mixed $stream, int $length = 1): string|false
    {
        return fread($stream, $length);
    }

    public function fwrite(mixed $stream, string $data, null|int $length = null): int|false
    {
        return fwrite($stream, $data, $length);
    }

    public function fseek(mixed $stream, int $offset, int $whence = SEEK_SET): int
    {
        return fseek($stream, $offset, $whence);
    }

    public function ftell(mixed $stream): int|false
    {
        return ftell($stream);
    }

    public function ftruncate(mixed $stream, int $size): bool
    {
        return ftruncate($stream, $size);
    }

    public function fstat(mixed $stream): array|false
    {
        return fstat($stream);
    }

    public function fileGetContents(
        string $filename,
        bool $useIncludePath = false,
        mixed $context = null,
        int $offset = 0,
        null|int $length = null,
    ): string|false {
        return file_get_contents(
            $filename,
            $useIncludePath,
            $context,
            $offset,
            $length,
        );
    }

    public function filePutContents(
        string $filename,
        mixed $data,
        int $flags = 0,
        mixed $context = null,
    ): int|false {
        return file_put_contents(
            $filename,
            $data,
            $flags,
            $context,
        );
    }

    /**
     * @throws IOException
     */
    public function mkdir(string $path, int $permission = 0777, bool $recursive = false, mixed $context = null): void
    {
        $mkdirStatus = mkdir($path, $permission, $recursive, $context);

        if ($mkdirStatus === false || $this->isDir($path) === false) {
            throw new IOException('Failed to make dir');
        }
    }

    public function isFile(string $filename): bool
    {
        return is_file($filename);
    }

    public function isReadable(string $filename): bool
    {
        return is_readable($filename);
    }

    public function isWriteable(string $filename): bool
    {
        return is_writeable($filename);
    }

    public function pathinfo(string $path, int $flags = PATHINFO_ALL): array|string
    {
        return pathinfo($path, $flags);
    }

    public function realpath(string $path): string|false
    {
        return realpath($path);
    }

    public function glob(string $pattern, int $flags = 0): array|false
    {
        return glob($pattern, $flags);
    }

    public function rename(string $from, string $to, mixed $context = null): bool
    {
        return rename($from, $to, $context);
    }

    public function rewind(mixed $stream): bool
    {
        return rewind($stream);
    }

    public function rmdir(string $directory, mixed $context = null): bool
    {
        return rmdir($directory, $context);
    }

    public function unlink(string $filename, mixed $context = null): bool
    {
        return unlink($filename, $context);
    }

    public function copyDirectory(string $from, string $to): void
    {
        if ($this->isDir($to)) {
            throw new IOException('Destination directory already exist');
        }

        $this->mkdir($to);
        $directory = $this->dir($from);

        foreach ($this->scanDir($directory) as $item) {
            if (in_array($item, ['.', '..'], true)) {
                continue;
            }

            $pathItem = $from . $item;

            if ($this->isDir($pathItem)) {
                $this->copyDirectory($pathItem, $to . $item);

                continue;
            }

            $this->copy($pathItem, $to . '/' . $item);
        }

        $directory->close();
    }

    public function cp(string $from, string $to): void
    {
        if ($this->isDir($from)) {
            $this->copyDirectory($from, $to);

            return;
        }

        $this->copy($from, $to);
    }

    public function mv(string $from, string $to): void
    {
        $this->rename($from, $to);
    }

    public function ls(string $path = '.'): array
    {
        $dirContent = [];
        $dir = $this->dir($path);

        foreach($this->scanDir($dir) as $item) {
            $dirContent[] = $item;
        }

        $dir->close();

        return $dirContent;
    }

    public function rm(string $path, bool $recursive = false, mixed $context = null): bool
    {
//        $test = new RecursiveDirectoryIterator($path);
//        if ($test->isDir()) {
//            foreach ($test as $item) {
//                $item->
//            }
//        } else {
//            $this->unlink($test->getRealPath());
//        }
        if ($this->isDir($path)) {
            $dir = $this->dir($path);

            foreach($this->scanDir($dir) as $item) {
                if (in_array($item, ['.', '..'], true) === false) {
                    $this->rm(realpath($path) . '/' . $item, true);
                }
            }

            return $this->rmdir($path, $context);
        }

        return $this->unlink($path, $context);
    }
}
