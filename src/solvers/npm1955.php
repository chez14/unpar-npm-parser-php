<?php

namespace Chez14\NpmParser\Solvers;

use Chez14\NpmParser\SolverInterface;
use Chez14\NpmParser\Transformer;
use Exception\BadEnrollmentYear;
use Exception\NotParseable;

/**
 * Solver untuk NPM yang sudah ada sejak UNPAR berdiri.
 * 
 * Bentuk NPM yang terbit adalah (tahun)(jurusan)(kode-unik).
 * Sebagai contoh: 2016730011.
 * 
 * Informasi didapatkan dari Rikie Setiawan (Administrasi FTIS).
 * See @link https://pmb.unpar.ac.id/menu/program_studi/8
 */
class NPM1995 implements SolverInterface
{
    public static
        $jenjang = [
            "5"=> "D3",
            "6"=> "S1",
            "7"=> "Profesi",
            "8"=> "S2",
            "9"=> "S3"
        ];

    public static
        $fakultas = [
            "1" => "Ekonomi",
            "2" => "Hukum",
            "3" => "Ilmu Sosial dan Ilmu Politik",
            "4" => "Teknik",
            "5" => "Filsafat",
            "6" => "Teknologi Industri",
            "7" => "Teknologi Informasi dan Sains",
            "8" => "Sekolah Pascasarjana"
        ];

    public static
        /**
         * Array of Jurusans on Kurikulum 2013. For students enrollment year <=2017.
         */
        $jurusan = [
            "110" => ["Ekonomi Pembangunan"],
            "120" => ["Manajemen"],
            "130" => ["Akutansi"],
            "910" => ["Manajemen Perusahaan", "jenjang" => "5", "fakultas" => "1"],

            "200" => ["Ilmu Hukum"],

            "310" => ["Ilmu Administrasi Publik"],
            "320" => ["Ilmu Administrasi Bisnis"],
            "330" => ["Ilmu Hubungan Internasional"],

            "410" => ["Teknik Sipil"],
            "420" => ["Arsitektur"],

            "510" => ["Ilmu Filsafat"],

            "610" => ["Teknik Industri"],
            "620" => ["Teknik Kimia"],
            "630" => ["Teknik Elektro"],

            "710" => ["Matematika"],
            "720" => ["Fisika"],
            "730" => ["Teknik Informatika"],

            "801" => ["Ilmu Administrasi Bisnis", "jenjang" => "8"],
            "811" => ["Manajemen", "jenjang" => "8"],
            "812" => ["Ilmu Ekonomi", "jenjang" => "9"],
            "821" => ["Ilmu Hukum", "jenjang" => "8"],
            "822" => ["Ilmu Hukum", "jenjang" => "9"],
            "831" => ["Teknik Sipil", "jenjang" => "8"],
            "832" => ["Ilmu Teknik Sipil", "jenjang" => "9"],
            "841" => ["Arsitektur", "jenjang" => "8"],
            "842" => ["Arsitektur", "jenjang" => "9"],
            "851" => ["Ilmu Sosial", "jenjang" => "8"],
            "861" => ["Ilmu Teologi", "jenjang" => "8"],
            "871" => ["Teknik Kimia", "jenjang" => "8"],
            "881" => ["Magister Teknik Industri", "jenjang" => "8"],
            "891" => ["Magister Hubungan Internasional", "jenjang" => "8"],
        ];


    public function isParseable(string $npm): bool
    {
        try {
            return $this->precheck($this->parse($npm));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Will try to parse this NPM. Will return a bunch of arrays if it's successfull,
     * and will throw a NotParseable Exception if it's malformed, or BadEnrollmentYear
     * if this solver couldn't handle it.
     *
     * @param string $npm
     * @return array
     */
    public function parse(string $npm): array
    {
        if (strlen($npm) != 10) {
            throw new NotParseable();
        }
        // 2000 730 000
        // 0123 456 789
        return [
            "enrollment_year" => substr($npm, 0, 4),
            "prodi_id" => substr($npm, 4, 3),
            "no_urut" => substr($npm, 7, 3)
        ];
    }

    /**
     * Will do some prechecks before finalizing things.
     *
     * @param string $npm Npm to check.
     * @return boolean true if all pass. A frustating Exception if not good.
     */
    protected function precheck(array $npms): bool
    {
        $result = $npms;
        if (!($result['enrollment_year'] < 2018 && $result['enrollment_year'] > 1994)) {
            throw new BadEnrollmentYear();
        }
        if (!array_key_exists($result['prodi_id'], self::$jurusan)) {
            throw new NotParseable();
        }
        return true;
    }

    public function getInfo(string $npm, bool $force = false): array
    {
        $result = null;
        try {
            $result = $this->parse($npm);
            $this->precheck($result);
        } catch (BadEnrollmentYear $e) {
            if (!$force) {
                throw $e;
            }
        }

        $result['jurusan'] = self::$jurusan[$result['prodi_id']][0];

        //fakultas
        $fakultas = $result['prodi_id'][0];
        if (array_key_exists("fakultas", self::$jurusan[$result['prodi_id']])) {
            $fakultas = self::$jurusan[$result['prodi_id']]['fakultas'];
        }
        $result['fakultas_id'] = $fakultas;
        $result['fakultas'] = self::$fakultas[$fakultas];

        //jenjang
        $jenjang = '6';
        if (array_key_exists("jenjang", self::$jurusan[$result['prodi_id']])) {
            $jenjang = self::$jurusan[$result['prodi_id']]['jenjang'];
        }
        $result['jenjang_id'] =$jenjang;
        $result['jenjang'] = self::$jenjang[$jenjang];
        
        try {
            $result = Transformer::toNpm2018($result);
        } catch (\Exception\NotTransformable $e) {
            if(!$force) {
                throw new NotParseable("Not transformable", 0, $e);
            }
        }
        return $result;
    }
}
