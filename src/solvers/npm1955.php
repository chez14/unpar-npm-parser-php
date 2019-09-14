<?php

namespace Chez14\NpmParser\Solvers;

use Chez14\NpmParser\SolverInterface;
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

    protected static
        $regex = "/([0-9]{4})([0-9]{3})([0-9]{3})/";

    public static
        $jenjang = [
            "D3",
            "S1",
            "Profesi",
            "S2",
            "S3",
            "D3",
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
            "910" => ["Manajemen Perusahaan", "jenjang" => 0, "fakultas" => "1"],

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

            "801" => ["Ilmu Administrasi Bisnis", "jenjang" => 3],
            "811" => ["Manajemen", "jenjang" => 3],
            "812" => ["Ilmu Ekonomi", "jenjang" => 4],
            "821" => ["Ilmu Hukum", "jenjang" => 3],
            "822" => ["Ilmu Hukum", "jenjang" => 4],
            "831" => ["Teknik Sipil", "jenjang" => 3],
            "832" => ["Ilmu Teknik Sipil", "jenjang" => 4],
            "841" => ["Arsitektur", "jenjang" => 3],
            "842" => ["Arsitektur", "jenjang" => 4],
            "851" => ["Ilmu Sosial", "jenjang" => 3],
            "861" => ["Ilmu Teologi", "jenjang" => 3],
            "871" => ["Teknik Kimia", "jenjang" => 3],
            "881" => ["Magister Teknik Industri", "jenjang" => 3],
            "891" => ["Magister Hubungan Internasional", "jenjang" => 3],
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
            "jurusan_id" => substr($npm, 4, 3),
            "npm" => substr($npm, 7, 3)
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
        if (!array_key_exists($result['jurusan_id'], self::$jurusan)) {
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

        $result['jurusan'] = self::$jurusan[$result['jurusan_id']][0];

        //fakultas
        $fakultas = $result['jurusan_id'][0];
        if (array_key_exists("fakultas", self::$jurusan[$result['jurusan_id']])) {
            $fakultas = self::$jurusan[$result['jurusan_id']]['fakultas'];
        }
        $result['fakultas_id'] = $fakultas;
        $result['fakultas'] = self::$fakultas[$fakultas];

        //jenjang
        $jenjang = self::$jenjang['1'];
        if (array_key_exists("jenjang", self::$jurusan[$result['jurusan_id']])) {
            $fakultas = self::$jenjang[self::$jurusan[$result['jurusan_id']]['jenjang']];
        }
        $result['jenjang'] = $jenjang;

        return $result;
    }
}
