<?php

use Exception\BadEnrollmentYear;
use Exception\NotParseable;
use PHPUnit\Framework\TestCase;

/**
 * @testdox NPM2018 Test
 */
class Npm2018Test extends TestCase
{
    /**
     * @testdox Npm2018 able to parse
     */
    public function testNpm2018Parse()
    {
        $npm2018 = new \Chez14\NpmParser\Solvers\NPM2018();

        $parsed_npm = $npm2018->parse("6181801001");
        $this->assertSame("2018", $parsed_npm['enrollment_year']);
        $this->assertSame("18", $parsed_npm['prodi_id']);
        $this->assertSame("001", $parsed_npm['no_urut']);

        $parsed_npm = $npm2018->getInfo("6181801001");
        $this->assertSame("2018", $parsed_npm['enrollment_year']);
        $this->assertSame("18", $parsed_npm['prodi_id']);
        $this->assertSame("001", $parsed_npm['no_urut']);
        $this->assertSame("7", $parsed_npm['fakultas_id']);
    }


    /**
     * @testdox Npm2018 able to parse, even if it's out-of-range
     */
    public function testNpm2018ParseForce()
    {
        $npm2018 = new \Chez14\NpmParser\Solvers\NPM2018();

        $parsed_npm = $npm2018->parse("6181601011");
        $this->assertSame("2016", $parsed_npm['enrollment_year']);
        $this->assertSame("18", $parsed_npm['prodi_id']);
        $this->assertSame("011", $parsed_npm['no_urut']);

        $parsed_npm = $npm2018->getInfo("6181601011", true);
        $this->assertSame("2016", $parsed_npm['enrollment_year']);
        $this->assertSame("18", $parsed_npm['prodi_id']);
        $this->assertSame("011", $parsed_npm['no_urut']);
        $this->assertSame("7", $parsed_npm['fakultas_id']);
    }

    /**
     * @testdox Npm2018 able to detect bad enrollment year
     *  
     */
    public function testNpm2018Exception()
    {
        // out of enrollment year
        $npm2018 = new \Chez14\NpmParser\Solvers\NPM2018();
        $this->expectException(BadEnrollmentYear::class);
        $parsed_npm = $npm2018->getInfo("6181601001");
    }

    /**
     * @testdox Npm2018 able to detect bad jurusan
     *  
     */
    public function testNpm2018Exception1()
    {
        // bad jurusan
        $npm2018 = new \Chez14\NpmParser\Solvers\NPM2018();
        $this->expectException(NotParseable::class);
        $parsed_npm = $npm2018->getInfo("6301801001");
    }
    /**
     * @testdox Npm2018 able to detect npm malformat
     *  
     */
    public function testNpm2018Exception3()
    {
        // genuine malformat
        $npm2018 = new \Chez14\NpmParser\Solvers\NPM2018();
        $this->expectException(NotParseable::class);
        $parsed_npm = $npm2018->getInfo("6181801");
    }

    /**
     * @testdox Npm2018 able to detect bad malformat and enrollment year, but should throw the NotParseable first
     *  
     */
    public function testNpm2018Exception4()
    {
        // genuine malformat and bad enrollment year
        $npm2018 = new \Chez14\NpmParser\Solvers\NPM2018();
        $this->expectException(NotParseable::class);
        $parsed_npm = $npm2018->getInfo("61618010");
    }
}
