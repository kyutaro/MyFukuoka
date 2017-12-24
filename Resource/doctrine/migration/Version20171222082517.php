<?php

/*
 * This file is part of the MyFukuoka
 *
 * Copyright (C) [year] [author]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Eccube\Application;
use Eccube\Common\Constant;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Plugin\MyFukuoka\Entity\MyFukuoka;

class Version20171222082517 extends AbstractMigration {

    protected $entities = array(
        'Plugin\MyFukuoka\Entity\MyFukuoka'
    );

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) {        
        $this->createMyFukuokaData();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) {
        $app = Application::getInstance();
        $meta = $this->getMetadata($app['orm.em']);

        $tool = new SchemaTool($app['orm.em']);
        $schemaFromMetadata = $tool->getSchemaFromMetadata($meta);

        // テーブル削除
        foreach ($schemaFromMetadata->getTables() as $table) {
            if ($schema->hasTable($table->getName())) {
                $schema->dropTable($table->getName());
            }
        }

        // シーケンス削除
        foreach ($schemaFromMetadata->getSequences() as $sequence) {
            if ($schema->hasSequence($sequence->getName())) {
                $schema->dropSequence($sequence->getName());
            }
        }
    }

    /**
     * 予め用意したデータを流し込む
     */
    private function createMyFukuokaData() {
        $app = Application::getInstance();
        $em = $app['orm.em'];
        
        // 2つデータを投入
        $MyFukuoka = new MyFukuoka();
        $MyFukuoka->setMyFukuokaContents('コンテンツ1の項目');
        $MyFukuoka->setMyFukuokaContents02('コンテンツ2の項目');
        $MyFukuoka->setMyFukuokaContents03('コンテンツ3の項目');
        $MyFukuoka->setDelFlg(Constant::DISABLED);
        $em->persist($MyFukuoka);
        $em->flush($MyFukuoka);

        $MyFukuoka02 = new MyFukuoka();
        $MyFukuoka02->setMyFukuokaContents('コンテンツ1の項目');
        $MyFukuoka02->setMyFukuokaContents02('コンテンツ2の項目');
        $MyFukuoka02->setMyFukuokaContents03('コンテンツ3の項目');
        $MyFukuoka02->setDelFlg(Constant::DISABLED);
        $em->persist($MyFukuoka02);
        $em->flush($MyFukuoka02);
    }

    /**
     * @param EntityManager $em
     * @return array
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    protected function getMetadata(EntityManager $em) {
        $meta = array();
        foreach ($this->entities as $entity) {
            $meta[] = $em->getMetadataFactory()->getMetadataFor($entity);
        }

        return $meta;
    }

}
