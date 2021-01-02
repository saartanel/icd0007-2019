<?php

require_once '../vendor/common.php';

const BASE_URL = 'http://localhost:8080';

class Ex1Tests extends WebTestCaseExtended {

    function first() {
        $this->get(BASE_URL);

        $this->ensureRelativeLink("b.html");
        $this->clickLink("b.html");
        $this->assertCurrentUrlEndsWith("/a/b/b.html");

        $this->ensureRelativeLink("e.html");
        $this->clickLink("e.html");
        $this->assertCurrentUrlEndsWith("/a/b/c/d/e/e.html");

        $this->ensureRelativeLink("d.html");
        $this->clickLink("d.html");
        $this->assertCurrentUrlEndsWith("/a/b/c/d/d.html");

        $this->ensureRelativeLink("b.html");
        $this->clickLink("b.html");
        $this->assertCurrentUrlEndsWith("/a/b/b.html");

        $this->ensureRelativeLink("b.html");
        $this->clickLink("index.html");
        $this->assertCurrentUrlEndsWith("/index.html");

    }
}

(new Ex1Tests())->run(new PointsReporter(6, 5));
