<?php

declare(strict_types=1);

namespace De\SWebhosting\TYPO3Surf\Tests;

class BuildTools
{
    /**
     * @var string[]
     */
    private static $toolDirectories = [
        'composer-normalize',
        'php-codesniffer',
        'php-cs-fixer',
        'phpmd',
        'phpstan',
    ];

    public static function createToolsDirectories(): void
    {
        foreach (self::$toolDirectories as $toolDirectory) {
            $fullDirectory = 'tools' . DIRECTORY_SEPARATOR . $toolDirectory;

            if (is_dir($fullDirectory)) {
                continue;
            }

            mkdir($fullDirectory, 0775, true);
        }
    }
}
