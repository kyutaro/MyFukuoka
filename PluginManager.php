<?php

/*
 * This file is part of the MyFukuoka
 *
 * Copyright (C) 2017 Hisashi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\MyFukuoka;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\BlockPosition;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Plugin\AbstractPluginManager;
use Eccube\Util\Cache;
use Symfony\Component\Filesystem\Filesystem;

class PluginManager extends AbstractPluginManager {

    /**
     * @var string コピー元ブロックファイル
     */
    private $originBlock;

    /**
     * @var string ブロック名
     */
    private $blockName = 'フクオカブロック';

    /**
     * @var string ブロックファイル名
     */
    private $blockFileName = 'my_fukuoka_block';

    /**
     * PluginManager constructor.
     */
    public function __construct() {
        // コピー元ブロックファイル
        $this->originBlock = __DIR__ . '/Resource/template/Block/' . $this->blockFileName . '.twig';
    }

    /**
     * プラグインインストール時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function install($config, Application $app) {
        
    }

    /**
     * プラグイン削除時の処理
     *
     * @param $config
     * @param Application $app
     */
    public function uninstall($config, Application $app) {
        // ブロックの削除(DBから当該ブロックに関するレコードを削除)
        $this->removeDataBlock($app);

        // 当該ブロックのブロックファイルを削除
        if (file_exists($app['config']['block_realdir'] . '/' . $this->blockFileName . '.twig')) {
            $this->removeBlock($app);
        }
        
        //第四引数に0を指定すると、Resource\doctrine\migration\VersionYYYYMMDD.phpのdown()メソッドが実行される
        //第四引数に何も指定しないと、Resource\doctrine\migration\VersionYYYYMMDD.phpのup()メソッドが実行される
        //詳細については下記を参照
        //↓
        //eccube\vendor\doctrine\migrations\lib\Doctrine\DBAL\Migrations\Migration.php
        //のmigrate()メソッド
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code'], 0);
    }

    /**
     * プラグイン有効時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function enable($config, Application $app) {
        $this->copyBlock($app);
        // ブロックへ登録
        $this->createDataBlock($app);
        
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
    }

    /**
     * プラグイン無効時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function disable($config, Application $app) {
        $this->removeBlock($app);
        // ブロックの削除
        $this->removeDataBlock($app);
        
    }

    /**
     * プラグイン更新時の処理
     *
     * @param $config
     * @param Application $app
     * @throws \Exception
     */
    public function update($config, Application $app) {
        $this->copyBlock($app);
        
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
    }

    /**
     * テンプレートブロックをコピーする
     *
     * @param $app
     */
    private function copyBlock($app) {
        /*
         *  ファイルコピー
         *  Filesystemインスタンスを生成するには、下記をuseしておかなけらばならない
         *  Symfony\Component\Filesystem\Filesystem
         */
        $file = new Filesystem();
        /*
         *  ブロックファイルをコピー
         *  第一引数は作成元のtwigファイルのパス
         *  第二引数は作成するtwigファイルのパス
         */
        $file->copy($this->originBlock, $app['config']['block_realdir'] . '/' . $this->blockFileName . '.twig');
    }

    /**
     * ブロックを登録.
     *
     * @param \Eccube\Application $app
     *
     * @throws \Exception
     */
    private function createDataBlock($app) {
        $em = $app['orm.em'];

        try {
            $DeviceType = $app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_PC);

            // プラグインのブロックが既に存在するかをチェック
            $Block = $app['eccube.repository.block']->findOneBy(array('DeviceType' => $DeviceType, 'file_name' => $this->blockFileName));
            if (!$Block) {
                // プラグインのブロックが既に存在していなければ、登録処理を行う
                $Block = $app['eccube.repository.block']->findOrCreate(null, $DeviceType);

                $Block->setName($this->blockName)
                        ->setFileName($this->blockFileName)
                        ->setDeletableFlg(Constant::DISABLED)
                        ->setLogicFlg(1);
                $em->persist($Block);
                $em->flush($Block);
            }

            // プラグインのブロック位置が既に登録されているかをチェック
            $blockPos = $em->getRepository('Eccube\Entity\BlockPosition')->findOneBy(array('block_id' => $Block->getId()));
            if ($blockPos) {
                return;
            }

            // ブロック位置の登録
            $blockPos = $em->getRepository('Eccube\Entity\BlockPosition')->findOneBy(
                    array('page_id' => 1, 'target_id' => PageLayout::TARGET_ID_MAIN_BOTTOM), array('block_row' => 'DESC')
            );
            $BlockPosition = new BlockPosition();
            // ブロックの順序を変更
            $BlockPosition->setBlockRow(1);
            if ($blockPos) {
                $blockRow = $blockPos->getBlockRow() + 1;
                $BlockPosition->setBlockRow($blockRow);
            }

            // ページレイアウトへの登録
            $PageLayout = $app['eccube.repository.page_layout']->find(1);

            $BlockPosition->setPageLayout($PageLayout)
                    ->setPageId($PageLayout->getId())
                    ->setTargetId(PageLayout::TARGET_ID_MAIN_BOTTOM)
                    ->setBlock($Block)
                    ->setBlockId($Block->getId())
                    ->setAnywhere(Constant::ENABLED);

            $em->persist($BlockPosition);
            $em->flush($BlockPosition);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove block template.
     *
     * @param $app
     */
    private function removeBlock($app) {
        $file = new Filesystem();
        /**
         *  ブロックファイルを削除
         *  引数は削除するtwigファイルのパス
         */
        $file->remove($app['config']['block_realdir'] . '/' . $this->blockFileName . '.twig');
    }

    /**
     * ブロックを削除.
     *
     * @param \Eccube\Application $app
     *
     * @throws \Exception
     */
    private function removeDataBlock($app) {
        // Blockの取得(file_nameはアプリケーションの仕組み上必ずユニーク)
        /** @var \Eccube\Entity\Block $Block */
        $Block = $app['eccube.repository.block']->findOneBy(array('file_name' => $this->blockFileName));

        if (!$Block) {
            Cache::clear($app, false);

            return;
        }

        $em = $app['orm.em'];
        try {
            // BlockPositionの削除
            $blockPositions = $Block->getBlockPositions();
            /** @var \Eccube\Entity\BlockPosition $BlockPosition */
            foreach ($blockPositions as $BlockPosition) {
                $Block->removeBlockPosition($BlockPosition);
                $em->remove($BlockPosition);
            }

            // Blockの削除
            $em->remove($Block);
            $em->flush();
        } catch (\Exception $e) {
            throw $e;
        }

        Cache::clear($app, false);
    }

}
