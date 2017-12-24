<?php

/*
 * This file is part of the MyFukuoka
 *
 * Copyright (C) 2017 Hisashi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\MyFukuoka\ServiceProvider;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Plugin\MyFukuoka\Form\Type\MyFukuokaConfigType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class MyFukuokaServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
//        // レポジトリ登録
//        $app['eccube.plugin.my_fukuoka.repository.my_fukuoka'] = $app->share(function () use ($app) {
//            return $app['orm.em']->getRepository('Plugin\MyFukuoka\Entity\MyFukuoka');
//        });

        // ブロック。接頭にblock_と付けねばフロントに表示されない
        $app->match('/block/my_fukuoka_block', '\Plugin\MyFukuoka\Controller\Block\MyFukuokaBlockController::index')
            ->bind('block_my_fukuoka_block');
        
        // Repository

        // Service

        // メッセージ登録
        // $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
        // $app['translator']->addResource('yaml', $file, $app['locale']);

        // load config
        // プラグイン独自の定数はconfig.ymlの「const」パラメータに対して定義し、$app['myfukuokaconfig']['定数名']で利用可能
        // if (isset($app['config']['MyFukuoka']['const'])) {
        //     $config = $app['config'];
        //     $app['myfukuokaconfig'] = $app->share(function () use ($config) {
        //         return $config['MyFukuoka']['const'];
        //     });
        // }

        // ログファイル設定
        $app['monolog.logger.myfukuoka'] = $app->share(function ($app) {

            $logger = new $app['monolog.logger.class']('myfukuoka');

            $filename = $app['config']['root_dir'].'/app/log/myfukuoka.log';
            $RotateHandler = new RotatingFileHandler($filename, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                'myfukuoka_{date}',
                'Y-m-d'
            );

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::ERROR),
                    0,
                    true,
                    true,
                    Logger::INFO
                )
            );

            return $logger;
        });

    }

    public function boot(BaseApplication $app)
    {
    }

}
