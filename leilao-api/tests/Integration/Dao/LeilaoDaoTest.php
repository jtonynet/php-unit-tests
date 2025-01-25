<?php

namespace Alura\Leilao\Tests\Integration\Dao;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
    /** @var \PDO */
    private static $pdo;

    public static function setUpBeforeClass(): void
    {
        // $this->pdo = ConnectionCreator::getConnection();
        self::$pdo = new \PDO('sqlite:memory');
        self::$pdo->beginTransaction();

        $sql = 'DROP TABLE IF EXISTS leiloes;';
        self::$pdo->exec($sql);

        $sql = 'CREATE TABLE leiloes 
            (
                id INTEGER PRIMARY KEY,
                descricao TEXT,
                finalizado BOOL,
                dataInicio TEXT
            );';
        self::$pdo->exec($sql);
    }

    public function testInsercaoEBuscaDevemFuncionar()
    {

        $leilao = new Leilao('Variant 0KM');
        $leilaoDao = new LeilaoDao(self::$pdo);

        $leilaoDao->salva($leilao);

        $leiloes = $leilaoDao->recuperarNaoFinalizados();
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame(
            'Variant 0KM',
            $leiloes[0]->recuperarDescricao()
        );
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}
