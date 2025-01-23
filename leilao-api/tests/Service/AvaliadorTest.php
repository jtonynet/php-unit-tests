<?php

namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Service\Avaliador;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    private $leiloeiro;

    protected function setUp(): void
    {
        $this->leiloeiro =  new Avaliador();
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveEncontrarOMaiorValorDeLances(Leilao $leilao)
    {
        // Act - When
        $this->leiloeiro->avalia($leilao);

        $maiorValor = $this->leiloeiro->getMaiorValor();

        // Assert - Then
        $valorEsperado = 2500;
        $this->assertEquals($valorEsperado, $maiorValor);
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveEncontrarOMenorValorDeLances(Leilao $leilao)
    {
        // Act - When
        $this->leiloeiro->avalia($leilao);

        $menorValor = $this->leiloeiro->getMenorValor();

        // Assert - Then
        $valorEsperado = 1700;
        $this->assertEquals($valorEsperado, $menorValor);
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveBuscarTresMaioresValores(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maiores = $this->leiloeiro->getMaioresLances();
        $this->assertCount(3, $maiores);
        $this->assertEquals(2500, $maiores[0]->getValor());
        $this->assertEquals(2000, $maiores[1]->getValor());
        $this->assertEquals(1700, $maiores[2]->getValor());
    }

    public static function leilaoEmOrdemCrescente(): Leilao
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('Joao');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 1700));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));

        return $leilao;
    }

    public static function leilaoEmOrdemDecrescente(): Leilao
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('Joao');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($ana, 1700));

        return $leilao;
    }

    public static function leilaoEmOrdemAleatoria(): Leilao
    {
        $leilao = new Leilao('Fiat 147 0km');

        $maria = new Usuario('Maria');
        $joao = new Usuario('Joao');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($ana, 1700));

        return $leilao;
    }

    public static function entregaLeiloes(): array
    {
        return [
            'ordem-crescente' => [self::leilaoEmOrdemCrescente()],
            'ordem-decrescente' => [self::leilaoEmOrdemDecrescente()],
            'ordem-aleatoria' => [self::leilaoEmOrdemAleatoria()]
        ];
    }
}
