<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbaf0f614958604277995e807607c517e
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DuplicateFileFind\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DuplicateFileFind\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/DuplicateFileFind',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbaf0f614958604277995e807607c517e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbaf0f614958604277995e807607c517e::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}