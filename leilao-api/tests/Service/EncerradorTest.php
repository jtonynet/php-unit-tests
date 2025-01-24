<?php

namespace Alura\Leilao\Tests\Domain;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $fiat147 = new Leilao(
            'Fiat 147 0KM',
            new \DateTimeImmutable('8 days ago')
        );

        $variant = new Leilao(
            'Variant 1982 0KM',
            new \DateTimeImmutable('10 days ago')
        );


        $leilaoDao = $this->createMock(LeilaoDao::class);

        /*
        * TODO for didatic purpouse
        $leilaoDao = $this->getMockBuilder(LeilaoDao::class)
            ->disableOriginalConstructor()
            // ->setConstructorArgs([new \PDO('sqlite:memory:')])
            ->getMock();
        */

        $leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([$fiat147, $variant]);

        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withAnyParameters(
                [$fiat147],
                [$variant]
            );

        $leilaoDao->method('recuperarFinalizados')
            ->willReturn([$fiat147, $variant]);

        /** @disregard */
        $encerrador = new Encerrador($leilaoDao);

        $encerrador->encerra();

        $leiloes = [$fiat147, $variant];
        self::assertCount(2, $leiloes);
        self::assertTrue($leiloes[0]->estaFinalizado());
        self::assertTrue($leiloes[1]->estaFinalizado());
        self::assertEquals($leiloes[0]->recuperarDescricao(), 'Fiat 147 0KM');
        self::assertEquals($leiloes[1]->recuperarDescricao(), 'Variant 1982 0KM');
    }
}
