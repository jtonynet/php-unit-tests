<?php

namespace Alura\Leilao\Tests\Domain;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    private $encerrador;
    /** @var MockObject */
    private $enviadorEmail;
    private $leilaoFiat147;
    private $leilaoVariant;

    protected function setUp(): void
    {
        $this->leilaoFiat147 = new Leilao(
            'Fiat 147 0KM',
            new \DateTimeImmutable('8 days ago')
        );

        $this->leilaoVariant = new Leilao(
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
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);

        $leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withAnyParameters(
                [$this->leilaoFiat147],
                [$this->leilaoVariant]
            );

        $leilaoDao->method('recuperarFinalizados')
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariant]);

        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);

        /** @disregard */
        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorEmail);
    }

    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $this->encerrador->encerra();

        $leiloes = [$this->leilaoFiat147, $this->leilaoVariant];
        self::assertCount(2, $leiloes);
        self::assertTrue($leiloes[0]->estaFinalizado());
        self::assertTrue($leiloes[1]->estaFinalizado());
        self::assertEquals($leiloes[0]->recuperarDescricao(), 'Fiat 147 0KM');
        self::assertEquals($leiloes[1]->recuperarDescricao(), 'Variant 1982 0KM');
    }

    public function testDeveContinuarOProcessamentoAoEncontrarErroAoEnviarOEmail()
    {
        $e = new \DomainException('Erro ao enviar o e-mail');
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException($e);

        $this->encerrador->encerra();
    }
}
