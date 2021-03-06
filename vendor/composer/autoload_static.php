<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite59e153731823c8eb6ce2cc3d133729f
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Alamgir\\DemoPlugin\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Alamgir\\DemoPlugin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Alamgir\\DemoPlugin\\Admin\\Admin' => __DIR__ . '/../..' . '/includes/Admin/Admin.php',
        'Alamgir\\DemoPlugin\\Admin\\Menus' => __DIR__ . '/../..' . '/includes/Admin/Menus.php',
        'Alamgir\\DemoPlugin\\Admin\\Settings' => __DIR__ . '/../..' . '/includes/Admin/Settings.php',
        'Alamgir\\DemoPlugin\\Admin\\SettingsFields' => __DIR__ . '/../..' . '/includes/Admin/SettingsFields.php',
        'Alamgir\\DemoPlugin\\Ajax' => __DIR__ . '/../..' . '/includes/Ajax.php',
        'Alamgir\\DemoPlugin\\Assets' => __DIR__ . '/../..' . '/includes/Assets.php',
        'Alamgir\\DemoPlugin\\Install\\Installer' => __DIR__ . '/../..' . '/includes/Install/Installer.php',
        'Alamgir\\DemoPlugin\\REST\\DemoRestApi' => __DIR__ . '/../..' . '/includes/REST/DemoRestApi.php',
        'Alamgir\\DemoPlugin\\REST\\Manager' => __DIR__ . '/../..' . '/includes/REST/Manager.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite59e153731823c8eb6ce2cc3d133729f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite59e153731823c8eb6ce2cc3d133729f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite59e153731823c8eb6ce2cc3d133729f::$classMap;

        }, null, ClassLoader::class);
    }
}
