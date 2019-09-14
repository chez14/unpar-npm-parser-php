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
class NPM2018 implements SolverInterface
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
            "01"=>["Ilmu Ekonomi", "fakultas"=>"1"],
            "02"=>["Ekonomi Pembangunan", "fakultas"=>"1"],
            "03"=>["Manajemen", "fakultas"=>"1"],
            "04"=>["Akuntansi", "fakultas"=>"1"],
            "05"=>["Ilmu Hukum", "fakultas"=>"2"],
            "06"=>["Ilmu Sosial", "fakultas"=>"8"],
            "07"=>["Ilmu Administrasi Publik", "fakultas"=>"3"],
            "08"=>["Ilmu Administrasi Bisnis", "fakultas"=>"3"],
            "09"=>["Ilmu Hubungan Internasional", "fakultas"=>"3"],
            "10"=>["Teknik Sipil", "fakultas"=>"4"],
            "11"=>["Arsitektur", "fakultas"=>"4"],
            "12"=>["Ilmu Filsafat", "fakultas"=>"5"],
            "13"=>["Teknik Industri", "fakultas"=>"6"],
            "14"=>["Teknik Kimia", "fakultas"=>"6"],
            "15"=>["Teknik Elektro", "fakultas"=>"6"],
            "16"=>["Matematika", "fakultas"=>"7"],
            "17"=>["Fisika", "fakultas"=>"7"],
            "18"=>["Teknik Informatika", "fakultas"=>"7"],
            "19"=>["Humaniora", "fakultas"=>null]
        ];

    public static
        $jenis_mahasiswa = [
            "01"=>"Reguler",
            "02"=>"Non Reguler",
            "03"=>"Acicis",
            "04"=>"Joint Degree"
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
        // 6 02 18 01 001
        // 0 12 34 56 789
        return [
            "jenjang" => substr($npm, 0, 1),
            "prodi_id" => substr($npm, 1, 2),
            "enrollment_year" => "20" . substr($npm, 3, 2),
            "jenis_mahasiswa" => substr($npm, 5, 2),
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
        if (intval($result['enrollment_year']) < 2018) {
            throw new BadEnrollmentYear();
        }
        if (!array_key_exists($result['prodi_id'], self::$jurusan)) {
            throw new NotParseable();
        }
        if (!array_key_exists($result['jenjang'], self::$jenjang)) {
            throw new NotParseable();
        }
        if (!array_key_exists($result['jenis_mahasiswa'], self::$jenis_mahasiswa)) {
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
        $fakultas = self::$jurusan[$result['prodi_id']]['fakultas'];
        if($result['jenjang'] == "8" || $result['jenjang'] == "9") {
            $fakultas = "8";
        }
        $result['fakultas_id'] = $fakultas;
        $result['fakultas'] = $fakultas?self::$fakultas[$fakultas]:null;

        //jenjang
        $jenjang = $result['jenjang'];
        $result['jenjang'] = self::$jenjang[$jenjang];

        return $result;
    }
}
