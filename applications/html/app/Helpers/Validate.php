<?php

namespace App\Helpers;

class Validate
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
    public function validateCPF($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        // verificar se tem 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }

        // verificar se todos os dígitos são iguais
        if (preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }

        // calcular o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (10 - $i) * intval($cpf[$i]);
        }
        $digit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        // verificar o primeiro dígito verificador
        if (intval($cpf[9]) !== $digit) {
            return false;
        }

        // calcular o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (11 - $i) * intval($cpf[$i]);
        }
        $digit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);

        // verificar o segundo dígito verificador
        if (intval($cpf[10]) !== $digit) {
            return false;
        }

        // CPF é válido
        return true;
    }
}
