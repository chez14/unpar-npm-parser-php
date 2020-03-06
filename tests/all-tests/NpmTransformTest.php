<?php

use Chez14\NpmParser\Solver;
use Exception\BadEnrollmentYear;
use Exception\NotParseable;
use PHPUnit\Framework\TestCase;

/**
 * @testdox NPMParse Test
 */
class NpmTransformTest extends TestCase
{
    /**
     * @testdox Solvers able to parse
     */
    public function testNpmParse()
    {
        $npm = Solver::getInfo("2015720002");
        $this->assertNotNull($npm);
        $npm = Solver::getInfo("6171901002");
        $this->assertNotNull($npm);
    }
}
