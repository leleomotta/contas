<?php

namespace App\Models;

class Imagem {

    public static function CriaImagem($vetor,$posicao){
        $arquivo = $vetor[$posicao]->getPathname();
        $tamanho = $vetor[$posicao]->getSize();
        $fp = fopen($arquivo, "rb");
        $conteudo = fread($fp, $tamanho);
        fclose($fp);
        return $conteudo;
    }

}
