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
 * Random select answers question renderer class.
 *
 * @package   qtype_answersselect
 * @copyright 2021 Joseph Rézeau <joseph@rezeau.org>
 * @copyright based on work by 2008 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/multichoice/renderer.php');
require_once($CFG->dirroot . '/question/type/answersselect/lib.php');

/**
 * Random select answers question renderer class.
 *
 * @copyright 2021 Joseph Rézeau <joseph@rezeau.org>
 * @copyright based on work by 2008 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_answersselect_renderer extends qtype_multichoice_multi_renderer {

    /**
     * Generate a brief statement of how many sub-parts of this question the
     * student got right.
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    public function num_parts_correct(question_attempt $qa) {

        if ($qa->get_question()->get_num_selected_choices($qa->get_last_qt_data()) >
            $qa->get_question()->get_num_correct_choices()) {
            return get_string('toomanyselected', 'qtype_multichoice');
        }
        $a = new stdClass();
        list($a->num, $a->outof) = $qa->get_question()->get_num_parts_right($qa->get_last_qt_data());
        if (is_null($a->outof)) {
            return '';
        }
        if ($a->num == 1) {
            return get_string('yougot1right', 'qtype_answersselect');
        }
        $f = new NumberFormatter(current_language(), NumberFormatter::SPELLOUT);
        $a->num = $f->format($a->num);
        return get_string('yougotnright', 'qtype_answersselect', $a);
    }

    /**
     * Get correct response
     *
     * @param question_attempt $qa
     * @return string
     */
    public function correct_response(question_attempt $qa) {
        $randomorderslectorder = explode(',', $qa->get_step(0)->get_qt_var('_order'));
        $question = $qa->get_question();
        $right = [];
        foreach ($question->answers as $ansid => $ans) {
            // Do not display correct answers which have not been randomly selected in the question.
            if (!in_array($ans->id, $randomorderslectorder)) {
                continue;
            }
            if ($ans->fraction > 0) {
                $t = $question->format_text($ans->answer, $ans->answerformat,
                        $qa, 'question', 'answer', $ansid);
                /* Trick to clean answers created with ATTO editor of LTR value
                *  Used as a replacement for default function make_html_inline($html).
                */
                $replacement = '';
                $patterns = ['/<p.*?\>/', '/<\/p>/', '/(<br|br \/)>/'];
                $t = preg_replace($patterns, $replacement, $t, -1 );
                $right[] = $t;
            }
        }
        return $this->correct_choices_answersselect($right, $question->correctchoicesseparator);
    }

    /**
     * Function returns string based on number of correct answers
     * Overrides the default MULTICHOICE correct_choices function
     * @param array $right An Array of correct responses to the current question
     * @param number $correctchoicesseparator The type of separator
     * @return string based on number of correct responses
     */
    protected function correct_choices_answersselect(array $right, $correctchoicesseparator) {
        // Return appropriate string for single/multiple correct answer(s).
        // Refactored in April 2024.
        if (count($right) == 1) {
                return get_string('correctansweris', 'qtype_multichoice',
                        implode(', ', $right));
        } else {
            if ($correctchoicesseparator == 0) {
                $separator = ', ';
            } else if ($correctchoicesseparator == 1) {
                $separator = ' ';
            } else {
                $separator = '<br />';
            };
            if ($correctchoicesseparator == 1) {
                return get_string('correctansweris', 'qtype_multichoice',
                        '<br />'.implode($separator, $right));
            } else {
                return get_string('correctanswersare', 'qtype_multichoice',
                        '<br />'.implode($separator, $right));
            }
        }
    }
}
