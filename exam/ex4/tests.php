<?php

require_once '../vendor/common.php';

class Ex4Tests extends UnitTestCaseExtended {

    function shouldPrintPersonsAndNumbersInCorrectOrder() {

        $output = $this->executeAndGetOutput('ex4.php');

        $this->assertPattern("/Alice: n1, n2/", $output);

        $this->assertPattern("/Alice: n5/", $output);

        $this->assertPattern("/Bob: n3/", $output);

        $this->assertPattern("/Carol: n4/", $output);

        // Order should be correct
        $this->assertPattern("/Alice.*Alice.*Bob.*Carol/s", $output);
    }

    private function executeAndGetOutput($scriptFile) {
        ob_start();

        include $scriptFile;

        return ob_get_clean();
    }
}

(new Ex4Tests())->run(new PointsReporter(10, 5));
