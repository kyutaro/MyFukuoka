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

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Application;

class Version20171222032533 extends AbstractMigration {

    /**
     * @var string table name
     */
    const NAME = 'plg_my_fukuoka';

    protected $entities = array(
        'Plugin\MyFukuoka\Entity\MyFukuoka'
    );


    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) {
        $table = $schema->getTable(self::NAME);
        if (!$table->hasColumn('my_fukuoka_contents_03')) {
            $table->addColumn('my_fukuoka_contents_03', 'text', array(
                'notnull' => false,
            ));
        }
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
