<?php

namespace Alura\Leilao\Model;

use Exception;

class Leilao
{
    /** @var Lance[] */
    private $lances;

    /** @var string */
    private $descricao;

    /** @var bool */
    private $finalizado = false;

    public function __construct(string $descricao)
    {
        $this->finalizado = false;
        $this->descricao = $descricao;
        $this->lances = [];
    }

    public function recebeLance(Lance $lance)
    {
        if (!empty($this->lances) && $this->ehDoUltimoUsuario($lance)) {
            throw new \DomainException('Usuario nao pode propor 2 lances consecutivos');
        }

        $totalLancesUsuario = $this->quantidadeDeLancesPorUsuario($lance->getUsuario());
        if ($totalLancesUsuario >= 5) {
            throw new \DomainException('Usuario nao pode propor mais de 5 lances por leilao');
        }

        $this->lances[] = $lance;
    }

    public function finaliza()
    {
        $this->finalizado = true;
    }

    public function estaFinalizado(): bool
    {
        return $this->finalizado;
    }

    private function ehDoUltimoUsuario(Lance $lance)
    {
        $ultimoLance = $this->lances[array_key_last($this->lances)]->getUsuario();
        return $lance->getUsuario() == $ultimoLance;
    }

    private function quantidadeDeLancesPorUsuario(Usuario $usuario): int
    {
        return array_reduce(
            $this->lances,
            function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
                if ($lanceAtual->getUsuario() == $usuario) {
                    return $totalAcumulado + 1;
                }

                return $totalAcumulado;
            },
            0
        );
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }
}
