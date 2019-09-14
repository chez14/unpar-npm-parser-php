<?php

use Exception\BadEnrollmentYear;
use Exception\NotParseable;
use PHPUnit\Framework\TestCase;

/**
 * @testdox NPM1995 Test
 */
class Npm1995Test extends TestCase
{
    /**
     * @testdox Npm1955 able to parse
     */
    public function testNpm1995Parse()
    {
        $npm1955 = new \Chez14\NpmParser\Solvers\NPM1995();

        $parsed_npm = $npm1955->parse("2016730011");
        $this->assertSame("2016", $parsed_npm['enrollment_year']);
        $this->assertSame("730", $parsed_npm['prodi_id']);
        $this->assertSame("011", $parsed_npm['npm']);

        $parsed_npm = $npm1955->getInfo("2016730011");
        $this->assertSame("2016", $parsed_npm['enrollment_year']);
        $this->assertSame("730", $parsed_npm['prodi_id']);
        $this->assertSame("011", $parsed_npm['npm']);
        $this->assertSame("7", $parsed_npm['fakultas_id']);
    }


    /**
     * @testdox Npm1955 able to parse, even if it's out-of-range
     */
    public function testNpm1995ParseForce()
    {
        $npm1955 = new \Chez14\NpmParser\Solvers\NPM1995();

        $parsed_npm = $npm1955->parse("2020730011");
        $this->assertSame("2020", $parsed_npm['enrollment_year']);
        $this->assertSame("730", $parsed_npm['prodi_id']);
        $this->assertSame("011", $parsed_npm['no_urut']);

        $parsed_npm = $npm1955->getInfo("2020730011", true);
        $this->assertSame("2020", $parsed_npm['enrollment_year']);
        $this->assertSame("730", $parsed_npm['prodi_id']);
        $this->assertSame("011", $parsed_npm['no_urut']);
        $this->assertSame("7", $parsed_npm['fakultas_id']);
    }

    /**
     * @testdox Npm1955 able to detect bad enrollment year
     *  
     */
    public function testNpm1995Exception()
    {
        // out of enrollment year
        $npm1955 = new \Chez14\NpmParser\Solvers\NPM1995();
        $this->expectException(BadEnrollmentYear::class);
        $parsed_npm = $npm1955->getInfo("2020730011");
    }

    /**
     * @testdox Npm1955 able to detect bad jurusan
     *  
     */
    public function testNpm1995Exception1()
    {
        // bad jurusan
        $npm1955 = new \Chez14\NpmParser\Solvers\NPM1995();
        $this->expectException(NotParseable::class);
        $parsed_npm = $npm1955->getInfo("2016780011");
    }

    /**
     * @testdox Npm1955 able to detect bad faculty
     *  
     */
    public function testNpm1995Exception2()
    {
        // bad faculty
        $npm1955 = new \Chez14\NpmParser\Solvers\NPM1995();
        $this->expectException(NotParseable::class);
        $parsed_npm = $npm1955->getInfo("2016930011");
    }

    /**
     * @testdox Npm1955 able to detect npm malformat
     *  
     */
    public function testNpm1995Exception3()
    {
        // genuine malformat
        $npm1955 = new \Chez14\NpmParser\Solvers\NPM1995();
        $this->expectException(NotParseable::class);
        $parsed_npm = $npm1955->getInfo("20167300");
    }

    /**
     * @testdox Npm1955 able to detect bad malformat and enrollment year, but should throw the NotParseable first
     *  
     */
    public function testNpm1995Exception4()
    {
        // genuine malformat and bad enrollment year
        $npm1955 = new \Chez14\NpmParser\Solvers\NPM1995();
        $this->expectException(NotParseable::class);
        $parsed_npm = $npm1955->getInfo("20207300");
    }
}
