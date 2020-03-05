<?php

namespace Chez14\NpmParser;

use Chez14\NpmParser\Solvers\NPM1995;
use Exception\NotTransformable;

class Transformer
{
    protected static
        $transform_9518 = [
            "110" => "602",
            "120" => "603",
            "130" => "604",
            "910" => "503",
            "200" => "605",
            "310" => "607",
            "320" => "608",
            "330" => "609",
            "410" => "610",
            "420" => "611",
            "510" => "612",
            "610" => "613",
            "620" => "614",
            "630" => "615",
            "710" => "616",
            "720" => "617",
            "730" => "618",
            "801" => "808",
            "811" => "803",
            "812" => "901",
            "821" => "805",
            "822" => "905",
            "831" => "810",
            "832" => "910",
            "841" => "811",
            "842" => "911",
            "851" => "806",
            "861" => "812",
            "871" => "814",
            "881" => "813",
            "891" => "809",
        ];

    protected static
        $transform_1895 = [
            "602" => "110",
            "603" => "120",
            "604" => "130",
            "503" => "910",
            "605" => "200",
            "607" => "310",
            "608" => "320",
            "609" => "330",
            "610" => "410",
            "611" => "420",
            "612" => "510",
            "613" => "610",
            "614" => "620",
            "615" => "630",
            "616" => "710",
            "617" => "720",
            "618" => "730",
            "808" => "801",
            "803" => "811",
            "901" => "812",
            "805" => "821",
            "905" => "822",
            "810" => "831",
            "910" => "832",
            "811" => "841",
            "911" => "842",
            "806" => "851",
            "812" => "861",
            "814" => "871",
            "813" => "881",
            "809" => "891",
        ];

    protected static
        $jenjang_1895 = [
            "5" => "0",
            "6" => "0",
            "8" => "1",
            "9" => "2"
        ];

    protected static
        $jenjang_9518 = [
            "0" => "5",
            "1" => "8",
            "2" => "9"
        ];

    public static function toNpm2018(array $npm): array
    {
        $result = self::pretransform($npm);

        if ($result['enrollment_year'] >= 2018) {
            return $npm;
        }

        if (!array_key_exists($result['jenjang_id_raw'], self::$jenjang_9518)) {
            throw new NotTransformable("Invalid `jenjang_id`");
        }

        $query = $result['prodi_id'];
        return array_merge($result, self::transform(self::$transform_9518, $query));
    }

    public static function toNpm1955(array $npm): array
    {
        $result = self::pretransform($npm);

        if ($result['enrollment_year'] < 2018) {
            return $npm;
        }

        if (!array_key_exists($result['jenjang_id_raw'], self::$jenjang_1895)) {
            throw new NotTransformable("Invalid `jenjang_id`");
        }

        $jenjang = self::$jenjang_9518[$result['jenjang_id_raw']];
        $query = $result['prodi_id'] . $jenjang;
        return array_merge($result, self::transform(self::$transform_1895, $query));
    }

    /**
     * Will auto prepare those npm for transformation.
     *
     * @param array $npm
     * @return array
     */
    protected static function pretransform(array $npm): array
    {
        if (!array_key_exists("jenjang_id", $npm)) {
            throw new NotTransformable("Required `jenjang_id`");
        }
        $result = $npm;
        $result['prodi_id_raw'] = $npm['prodi_id'];
        $result['jenjang_id_raw'] = $npm['jenjang_id'];
        if (!array_key_exists('jenis_mahasiswa_id', $npm)) {
            $result['jenis_mahasiswa_id'] = "01"; //default
        }
        return $result;
    }

    protected static function transform(array $dictionary, string $query): array
    {
        if (!array_key_exists($query, $dictionary)) {
            throw new NotTransformable("Invalid `jenjang_id` and `prodi_id` combination. Got " . $query);
        }
        $query = $dictionary[$query];

        $result["prodi_id"] = substr($query, 2, 2);
        $result["jenjang_id"] = substr($query, 1, 1);
        return $result;
    }
}
