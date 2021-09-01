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
 */
class qtype_answersselect_question_test extends basic_testcase {

    public function test_replace_char_at() {
        $this->assertEquals(qtype_answersselect_question::replace_char_at('220', 0, '0'), '020');
    }

    public function test_grade_responses_right_right() {
        $mc = test_question_maker::make_question('answersselect', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(array('choice0' => '1', 'choice2' => '1'));
        $this->assertEquals(1, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedright);
    }

    public function test_grade_responses_right() {
        $mc = test_question_maker::make_question('answersselect', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(array('choice0' => '1'));
        $this->assertEquals(0.5, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    public function test_grade_responses_wrong_wrong() {
        $mc = test_question_maker::make_question('answersselect', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(array('choice1' => '1', 'choice3' => '1'));
        $this->assertEquals(0, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedwrong);
    }

    public function test_grade_responses_right_wrong_wrong() {
        $mc = test_question_maker::make_question('answersselect', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(
                array('choice0' => '1', 'choice1' => '1', 'choice3' => '1'));
        $this->assertEquals(0, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    public function test_grade_responses_right_wrong() {
        $mc = test_question_maker::make_question('answersselect', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(array('choice0' => '1', 'choice1' => '1'));
        $this->assertEquals(0.5, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    public function test_grade_responses_right_right_wrong() {
        $mc = test_question_maker::make_question('answersselect', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(array(
                'choice0' => '1', 'choice2' => '1', 'choice3' => '1'));
        $this->assertEquals(0.5, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }

    public function test_grade_responses_right_right_wrong_wrong() {
        $mc = test_question_maker::make_question('answersselect', 'two_of_four');
        $mc->shuffleanswers = false;
        $mc->answersselectmode = 0;
        $mc->start_attempt(new question_attempt_step(), 1);

        list($fraction, $state) = $mc->grade_response(array(
                'choice0' => '1', 'choice1' => '1', 'choice2' => '1', 'choice3' => '1'));
        $this->assertEquals(0, $fraction, '', $this->tolerance);
        $this->assertEquals($state, question_state::$gradedpartial);
    }
}
