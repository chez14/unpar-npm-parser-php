<?php

namespace Chez14\NpmParser;

use Exception\NotParseable;

class Solver
{
    public static
        $parsers;

    protected static function getParsersInstance(): array
    {
        if (!self::$parsers) {
            self::$parsers = [
                new Solvers\NPM1995(),
                new Solvers\NPM2018()
            ];
        }
        return self::$parsers;
    }

    /**
     * Will try to match the parsers that are suitable for the NPM.
     *
     * @param string $npm Npm to be tested
     * @return SolverInterface
     */
    protected static function getSuitableParser(string $npm): SolverInterface
    {
        $parsers = self::getParsersInstance();
        foreach ($parsers as $p) {
            try {
                if ($p->parse($npm)) {
                    return $p;
                }
            } catch (\Exception $e) {
                // do nothing.
            }
        }
        throw new NotParseable("Thone NPM are not parseable by this solvers.");
    }

    /**
     * Get all information about this NPM. Including Jurusan, Fakultas, and Jenjang.
     *
     * @param string $npm
     * @return array a bunch of infos in an array with keys: `npm`, `jurusan`, 
     *      `prodi_id`, `fakultas`, `fakultas_id`, and `jenjang`.
     */
    public static function getInfo(string $npm): array
    {
        $parser = self::getSuitableParser($npm);

        return $parser->getInfo($npm);
    }

    /**
     * Will return angkatan info.
     *
     * @param string $npm NPM to be parsed
     * @return string angkatan
     */
    public static function getAngkatan(string $npm): string
    {
        $parser = self::getSuitableParser($npm);

        return $parser->parse($npm)['enrollment_year'];
    }
}
