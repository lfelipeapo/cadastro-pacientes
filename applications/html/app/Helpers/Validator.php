<?php

namespace App\Helpers;


class Validator
{

    /**
     * Verifies if CNS number is correct based in the
     * first digit if is 1 or 2.
     *
     * @param  string  $cns
     * @return bool
     */
    public function validaCns(string $cns): bool
    {
        if (strlen($cns) != 15) {
            return false;
        }

        $pis = substr($cns, 0, 11);
        $soma = 0;

        for ($i = 0; $i < 11; $i++) {
            $soma += intval($pis[$i]) * (15 - $i);
        }

        $resto = $soma % 11;
        $dv = 11 - $resto;

        if ($dv == 11) {
            $dv = 0;
        }

        if ($dv == 10) {
            $soma = 0;

            for ($i = 0; $i < 11; $i++) {
                $soma += intval($pis[$i]) * (15 - $i);
            }

            $resto = $soma % 11;
            $dv = 11 - $resto;

            if ($dv == 11) {
                $dv = 0;
            }

            if ($dv == 10) {
                return false;
            }

            $cns = substr_replace($cns, '001', 11, 3);
        } else {
            $cns = substr_replace($cns, '000', 11, 3);
        }

        return $cns[12] == $dv && $cns[13] == intval($dv . $cns[12]) % 11;
    }


    /**
     * Verifies if CNS number is correct based in the
     * first digit if is 7, 8 or 9.
     *
     * @param  string  $cns
     * @return bool
     */
    public function validaCnsProv(string $cns):bool
    {
        if (strlen($cns) != 15) {
            return false;
        }

        $soma = 0;

        for ($i = 0; $i < 15; $i++) {
            $soma += intval($cns[$i]) * (15 - $i);
        }

        $resto = $soma % 11;

        return $resto == 0;
    }

    /**
     * Verifies if CPF number is correct with rules of
     * validation.
     *
     * @param  string  $number
     * @return bool
     */
    public function validateCPF(string $number):bool
    {
        $cpf = preg_replace('/[^0-9]/', "", $number);

        if (strlen($cpf) != 11 || preg_match('/([0-9])\1{10}/', $cpf)) {
            return false;
        }

        for ($i = 9; $i < 11; $i++) {
            $sum = 0;
            for ($j = 0; $j < $i; $j++) {
                $sum += $cpf[$j] * ($i + 1 - $j);
            }
            $result = (($sum * 10) % 11);
            if ($cpf[$i] != $result) {
                return false;
            }
        }
    }
}
