<?php

require_once '../vendor/common.php';

const BASE_URL = 'http://localhost:8080';

class Ex3Tests extends WebTestCaseExtended {

    function defaultPageIsFrom() {
        $this->get(BASE_URL);

        $this->assertField('color');
    }

    function formSubmissionRedirects() {
        $this->get(BASE_URL);

        $this->setMaximumRedirects(0);

        $this->clickSubmitByName('button');

        $this->assertResponse([302]);
    }

    function redirectUrlIsCorrect() {
        $this->get(BASE_URL);

        $this->clickSubmitByName('button');

        $this->assertEqual($this->getQueryString(), '?color=red');
    }

    function pageContentIsCorrect() {
        $this->get(BASE_URL);

        $this->clickSubmitByName('button');

        $this->assertText('Valitud värv: Punane');
        $this->assertLinkById('back-link');
    }
}

(new Ex3Tests())->run(new PointsReporter(10, 5));
