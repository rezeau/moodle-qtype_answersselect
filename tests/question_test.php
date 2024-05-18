<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the answersselect question class.
 *
 * @package   qtype_answersselect
 * @copyright 2021 Joseph Rézeau <joseph@rezeau.org>
 * @copyright based on work by 2008 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_answersselect;

use qtype_answersselect_question;
use test_question_maker;
use question_attempt_step;
use question_state;
use qtype_answersselect_test_helper as helper;


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/answersselect/question.php');


/**
 * Unit tests for the answersselect question class.
 *
 * @copyright 2021 Joseph Rézeau <joseph@rezeau.org>
 * @copyright based on work by 2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers ::everything
 */
final class question_test extends \basic_testcase {

    /**
     * @var int
     */
    private $tolerance = 0.000001;

    /**
     * @var int
     */
    public $qtype;

    /**
     * ok
     */
    public function test_replace_char_at(): void {
        $this->assertEquals(qtype_answersselect_question::replace_char_at('220', 0, '0'), '020');
    }

    /**
     * ok
     */
    public function test_grade_responses_right_right(): void {
        $mc = test_question_maker::make_question('answersselect', 'mammals_two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(['choice0' => 'the cat', 'choice2' => 'the shark']);
        $this->assertEquals(1, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedright);
    }

    /**
     * ok
     */
    public function test_grade_responses_right(): void {
        $mc = test_question_maker::make_question('answersselect', 'mammals_two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);
        list($fraction, $state) = $mc->grade_response(['choice0' => 'the shark']);
        $this->assertEquals(0.5, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    /**
     * ok
     */
    public function test_grade_responses_wrong_wrong(): void {
        $mc = test_question_maker::make_question('answersselect', 'mammals_two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        $tolerance = !empty($this->tolerance) ? $this->tolerance : 0.0;

        list($fraction, $state) = $mc->grade_response(['choice1' => 'the shark', 'choice3' => 'the shark']);
        $this->assertEquals(0, $fraction, '', $tolerance);
        $this->assertEquals($state, question_state::$gradedwrong);
    }
    /**
     * ok
     *
     */
    public function test_grade_responses_right_wrong_wrong(): void {
        $mc = test_question_maker::make_question('answersselect', 'mammals_two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        $tolerance = !empty($this->tolerance) ? $this->tolerance : 0.0;

        list($fraction, $state) = $mc->grade_response(
                ['choice0' => 'the shark', 'choice1' => 'the shark', 'choice3' => 'the shark']);
        $this->assertEquals(0, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    /**
     * ok
     *
     */
    public function test_grade_responses_right_wrong(): void {
        $mc = test_question_maker::make_question('answersselect', 'mammals_two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        $tolerance = !empty($this->tolerance) ? $this->tolerance : 0.0;

        list($fraction, $state) = $mc->grade_response(['choice0' => 'the shark', 'choice1' => 'the shark']);
        $this->assertEquals(0.5, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    /**
     * ok
     *
     */
    public function test_grade_responses_right_right_wrong(): void {
        $mc = test_question_maker::make_question('answersselect', 'mammals_two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        $tolerance = !empty($this->tolerance) ? $this->tolerance : 0.0;

        list($fraction, $state) = $mc->grade_response([
                'choice0' => 'the shark', 'choice2' => 'the shark', 'choice3' => 'the shark']);
        $this->assertEquals(0.5, $fraction, '', $tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    /**
     * ok
     *
     */
    public function test_grade_responses_right_right_wrong_wrong(): void {
        $mc = test_question_maker::make_question('answersselect', 'mammals_two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        $tolerance = !empty($this->tolerance) ? $this->tolerance : 0.0;

        list($fraction, $state) = $mc->grade_response([
                'choice0' => 'the shark', 'choice1' => 'the shark', 'choice2' => 'the shark', 'choice3' => 'the shark']);
        $this->assertEquals(0, $fraction, '', $tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }
}
