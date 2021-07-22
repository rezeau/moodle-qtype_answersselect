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

    public function correct_response(question_attempt $qa) {
        $randomorderslectorder = explode(',', $qa->get_step(0)->get_qt_var('_order'));
        $question = $qa->get_question();
        $right = array();
        foreach ($question->answers as $ansid => $ans) {
            // Do not display correct answers which have not been randomly selected in the question.
            if (!in_array($ans->id, $randomorderslectorder)) {
                continue;
            }
            if ($ans->fraction > 0) {
                $right[] = $question->make_html_inline($question->format_text($ans->answer, $ans->answerformat,
                        $qa, 'question', 'answer', $ansid));
            }
        }
        return $this->correct_choices_answersselect($right, $question->correctchoicesseparator);
    }

    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $response = $question->get_response($qa);
        $inputname = $qa->get_qt_field_name('answer');
        $inputattributes = array(
            'type' => $this->get_input_type(),
            'name' => $inputname,
        );

        if ($options->readonly) {
            $inputattributes['disabled'] = 'disabled';
        }

        $radiobuttons = array();
        $feedbackimg = array();
        $feedback = array();
        $classes = array();

        foreach ($question->get_order($qa) as $value => $ansid) {
            $ans = $question->answers[$ansid];
            $inputattributes['name'] = $this->get_input_name($qa, $value);
            $inputattributes['value'] = $this->get_input_value($value);
            $inputattributes['id'] = $this->get_input_id($qa, $value);
            $inputattributes['aria-labelledby'] = $inputattributes['id'] . '_label';
            $isselected = $question->is_choice_selected($response, $value);
            if ($isselected) {
                $inputattributes['checked'] = 'checked';
            } else {
                unset($inputattributes['checked']);
            }
            $hidden = '';
            if (!$options->readonly && $this->get_input_type() == 'checkbox') {
                $hidden = html_writer::empty_tag('input', array(
                    'type' => 'hidden',
                    'name' => $inputattributes['name'],
                    'value' => 0,
                ));
            }

            $choicenumber = '';
            if ($question->answernumbering !== 'none') {
                $choicenumber = html_writer::span(
                        $this->number_in_style($value, $question->answernumbering), 'answernumber');
            }
            $choicetext = $question->format_text($ans->answer, $ans->answerformat, $qa, 'question', 'answer', $ansid);
            $choice = html_writer::div($choicetext, 'flex-fill ml-1');

            $radiobuttons[] = $hidden . html_writer::empty_tag('input', $inputattributes) .
                    html_writer::div($choicenumber . $choice, 'd-flex w-100', [
                        'id' => $inputattributes['id'] . '_label',
                        'data-region' => 'answer-label',
                    ]);

            // Param $options->suppresschoicefeedback is a hack specific to the
            // answersselect question type. It would be good to refactor to
            // avoid refering to it here.
            if ($options->feedback && empty($options->suppresschoicefeedback) &&
                    $isselected && trim($ans->feedback)) {
                $feedback[] = html_writer::tag('div',
                        $question->make_html_inline($question->format_text(
                                $ans->feedback, $ans->feedbackformat,
                                $qa, 'question', 'answerfeedback', $ansid)),
                        array('class' => 'specificfeedback'));
            } else {
                $feedback[] = '';
            }
            $class = 'r' . ($value % 2);
            if ($options->correctness && $isselected) {
                $feedbackimg[] = $this->feedback_image($this->is_right($ans));
                $class .= ' ' . $this->feedback_class($this->is_right($ans));
            } else {
                $feedbackimg[] = '';
            }
            $classes[] = $class;
        }

        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa),
                array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        if ($question->showstandardinstruction == 1) {
            $result .= html_writer::tag('div', $this->prompt(), array('class' => 'prompt'));
        }

        $result .= html_writer::start_tag('div', array('class' => 'answer'));
        foreach ($radiobuttons as $key => $radio) {
            $result .= html_writer::tag('div', $radio . ' ' . $feedbackimg[$key] . $feedback[$key],
                    array('class' => $classes[$key])) . "\n";
        }
        $result .= html_writer::end_tag('div'); // Answer.

        // Load JS module for the question answers.
        $this->page->requires->js_call_amd('qtype_multichoice/answers', 'init',
            [$qa->get_outer_question_div_unique_id()]);
        $result .= $this->after_choices($qa, $options);

        $result .= html_writer::end_tag('div'); // Ablock.

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($qa->get_last_qt_data()),
                    array('class' => 'validationerror'));
        }

        return $result;
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
        if (count($right) == 1) {
                return get_string('correctansweris', 'qtype_multichoice',
                        implode(', ', $right));
        } else if (count($right) > 1) {
            if ($correctchoicesseparator == 0) {
                $separator = ', ';
            } else if ($correctchoicesseparator == 1) {
                $separator = ' ';
            } else {
                $separator = '<br />';
            };
                return get_string('correctanswersare', 'qtype_multichoice',
                        '<br />'.implode($separator, $right));
        } else {
                return "";
        }
    }

}
