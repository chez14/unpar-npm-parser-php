<?php
namespace Chez14\NpmParser;

interface SolverInterface {
    /**
     * Make sure that this NPM can be extracted with this solver.
     *
     * @param string $npm the NPM.
     * @return boolean will return true if this solver is applicable.
     */
    public function isParseable(string $npm) : bool;

    /**
     * Extracts information from this NPM.
     *
     * @param string $npm the NPM.
     * @param bool $force Force parse the NPM.
     * @return array will return at least `npm`, `jurusan`, `jurusan_id` and `enrollment_year`.
     */
    public function getInfo(string $npm, bool $force = false) : array;
}