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
 * Random select answers question definition class.
 *
 * @package    qtype_answersselect
 * @copyright 2021 Joseph Rézeau <joseph@rezeau.org>
 * @copyright based on work by 2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/multichoice/question.php');

/**
 * Represents a Random select answers question.
 *
 * @copyright 2021 Joseph Rézeau <joseph@rezeau.org>
 * @copyright based on work by 2008 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_answersselect_question extends qtype_multichoice_multi_question
        implements question_automatically_gradable_with_countback {

    /**
     * @var int standard instruction to be displayed if enabled.
     */
    public $showstandardinstruction = 0;

    /**
     * Declare public variables to make PHP8.2 happy.
     */
    public $answersselectmode;
    public $randomselectcorrect;
    public $randomselectincorrect;
    public $hardsetamountofanswers;
    public $hastobeoneincorrectanswer;
    public $correctchoicesseparator;

    /**
     * Start a new attempt at this question, storing any information that will
     * be needed later in the step and doing initialisation
     *
     * @param question_attempt_step $step
     * @param number $variant (apparently not used)
     */
    public function start_attempt(question_attempt_step $step, $variant) {

        if ($this->answersselectmode == 0) {
            $this->order = array_keys($this->answers);
        } else {
            $this->order = $this->get_new_order();
        }

        if ($this->shuffleanswers) {
            shuffle($this->order);
        }

        $step->set_qt_var('_order', implode(',', $this->order));
    }

    /**
     * When an in-progress question_attempt is re-loaded from the
     * database, this method is called so that the question can re-initialise
     * its internal state as needed by this attempt.
     *
     * For example, the multiple choice question type needs to set the order
     * of the choices to the order that was set up when start_attempt was called
     * originally. All the information required to do this should be in the
     * $step object, which is the first step of the question_attempt being loaded.
     *
     * @param question_attempt_step  $step The first step of the question_attempt
     *      being loaded.
     */
    public function apply_attempt_state(question_attempt_step $step) {
        $this->order = explode(',', $step->get_qt_var('_order'));

        // Add any missing answers. Sometimes people edit questions after they
        // have been attempted which breaks things.
        foreach ($this->order as $ansid) {
            if (isset($this->answers[$ansid])) {
                continue;
            }
            $a = new stdClass();
            $a->id = 0;
            $a->answer = html_writer::span(get_string('deletedchoice', 'qtype_multichoice'),
                    'notifyproblem');
            $a->answerformat = FORMAT_HTML;
            $a->fraction = 0;
            $a->feedback = '';
            $a->feedbackformat = FORMAT_HTML;
            $this->answers[$ansid] = $this->qtype->make_answer($a);
            $this->answers[$ansid]->answerformat = FORMAT_HTML;
        }
    }


    /**
     *  Set renderer for ou multiple response
     *
     * @param moodle_page $page
     * @return renderer_base
     */
    public function get_renderer(moodle_page $page) {

        return $page->get_renderer('qtype_answersselect');
    }

    /**
     * Creat the appropriate behaviour for an attempt at this quetsion,
     * given the desired (archetypal) behaviour.
     *
     * @param question_attempt $qa the attempt we are creating a behaviour for.
     * @param string $preferredbehaviour the requested type of behaviour.
     * @return question_behaviour the new behaviour object.
     */
    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        if ($preferredbehaviour == 'interactive') {
            return question_engine::make_behaviour(
                    'interactivecountback', $qa, $preferredbehaviour);
        }
        return question_engine::make_archetypal_behaviour($preferredbehaviour, $qa);
    }

    /**
     * Categorise the student's response according to the categories defined by
     * get_possible_responses.
     * @param array $response a response, as might be passed to grade_response().
     * @return array subpartid => question_classified_response objects.
     *      returns an empty array if no analysis is possible.
     */
    public function classify_response(array $response) {
        $choices = parent::classify_response($response);
        $numright = $this->get_num_correct_choices();
        foreach ($choices as $choice) {
            $choice->fraction /= $numright;
        }
        return $choices;
    }

    /**
     * Grade a response to the question, returning a fraction between
     * get_min_fraction() and get_max_fraction(), and the corresponding question_state
     * right, partial or wrong.
     * @param array $response responses, as returned by question_attempt_step::get_qt_data().
     * @return array (float, integer) the fraction, and the state.
     */
    public function grade_response(array $response) {
        list($numright, $total) = $this->get_num_parts_right($response);
        $numwrong = $this->get_num_selected_choices($response) - $numright;
        $numcorrect = $this->get_num_correct_choices();
        $fraction = max(min($numright, $numcorrect - $numwrong), 0) / $numcorrect;

        $state = question_state::graded_state_for_fraction($fraction);
        if ($state == question_state::$gradedwrong && $numright > 0) {
            $state = question_state::$gradedpartial;
        }

        return array($fraction, $state);
    }

    /**
     * Disable those hint settings that we don't want when the student has selected
     * more choices than the number of right choices.
     * This avoids giving the game away.
     *
     * @param question_hint_with_parts $hint a hint.
     */
    protected function disable_hint_settings_when_too_many_selected(
            question_hint_with_parts $hint) {
        parent::disable_hint_settings_when_too_many_selected($hint);
        $hint->showchoicefeedback = false;
    }

    /**
     * Computes the final grade when "Multiple Attempts" or "Hints" are enabled

     * @param array $responses Contains the user responses. 1st dimension = attempt, 2nd dimension = answers
     * @param int $totaltries Not needed
     */
    public function compute_final_grade($responses, $totaltries) {
        $responsehistories = array();

        foreach ($this->order as $key => $ansid) {
            $fieldname = $this->field($key);
            $responsehistories[$ansid] = '';
            foreach ($responses as $response) {
                if (!array_key_exists($fieldname, $response) || !$response[$fieldname]) {
                    $responsehistories[$ansid] .= '0';
                } else {
                    $responsehistories[$ansid] .= '1';
                }
            }
        }

        return self::grade_computation($responsehistories, $this->answers,
                $this->penalty, $totaltries);
    }

    /**
     * Implement the scoring rules.
     * @param array $responsehistory
     * @param array $answers
     * @param int $penalty
     * @param int $questionnumtries
     * @return int the score
     */
    public static function grade_computation($responsehistory, $answers,
            $penalty, $questionnumtries) {
        // First we reverse the strings to get the most recent responses to the start, then
        // distinguish right and wrong by replacing 1 with 2 for right answers.
        $workspace = array();
        $numright = 0;

        foreach ($responsehistory as $id => $string) {
            $workspace[$id] = strrev($string);
            if (!question_state::graded_state_for_fraction(
                    $answers[$id]->fraction)->is_incorrect()) {
                $workspace[$id] = str_replace('1', '2', $workspace[$id]);
                $numright++;
            }
        }
        // Now we sort which should put answers more likely to help the candidate near the bottom of
        // workspace.
        sort($workspace);

        // Now, for each try we check to see if too many options were selected. If so, we
        // unselect correct answers in that, starting from the top of workspace - the ones that are
        // likely to turn out least favourable in the end.
        $actualnumtries = strlen(reset($workspace));
        for ($try = 0; $try < $actualnumtries; $try++) {
            $numselected = 0;
            foreach ($workspace as $string) {
                if (substr($string, $try, 1) != '0') {
                    $numselected++;
                }
            }
            if ($numselected > $numright) {
                $numtoclear = $numselected - $numright;
                $newworkspace = array();
                foreach ($workspace as $string) {
                    if (substr($string, $try, 1) == '2' && $numtoclear > 0) {
                        $string = self::replace_char_at($string, $try, '0');
                        $numtoclear--;
                    }
                    $newworkspace[] = $string;
                }
                $workspace = $newworkspace;
            }
        }

        // Now convert each string into a score. The score depends on the number of 2s at the start
        // of the string. Add extra 2s if the student got it right in fewer than the maximum
        // permitted number of tries.
        $triesnotused = $questionnumtries - $actualnumtries;
        foreach ($workspace as $string) {
            // Turn any remaining 1s to 0s for convinience.
            $string = str_replace('1', '0', $string);
            $num2s = strpos($string . '0', '0');
            if ($num2s > 0) {
                $num2s += $triesnotused;
                $scores[] = max(0, 1 / $numright * (1 - $penalty * ($questionnumtries - $num2s)));
            } else {
                $scores[] = 0;
            }
        }

        // Finally, sum the scores.
        return array_sum($scores);
    }

    /**
     * String function (not documented in the OU multiple response question type)
     *
     * @param string $string
     * @param int $pos
     * @param string $newchar
     * @return string
     */
    public static function replace_char_at($string, $pos, $newchar) {
        return substr($string, 0, $pos) . $newchar . substr($string, $pos + 1);
    }

    /**
     * Return the number of subparts of this response that are right.
     * @param array $response a response
     * @return array with two elements, the number of correct subparts, and
     * the total number of subparts.
     */
    public function get_num_parts_right(array $response) {
        $numright = 0;
        foreach ($this->order as $key => $ans) {
            $fieldname = $this->field($key);
            if (!array_key_exists($fieldname, $response) || !$response[$fieldname]) {
                continue;
            }

            if (!question_state::graded_state_for_fraction(
                    $this->answers[$ans]->fraction)->is_incorrect()) {
                $numright += 1;
            }
        }

        return array($numright, count($this->order));
    }

    /**
     * Return the number of choices that are correct.
     * @return int the number of choices that are correct.
     */
    public function get_num_correct_choices() {
        $numcorrect = 0;
        foreach ($this->answers as $ans) {
            if (in_array($ans->id, $this->order)) {
                if (!question_state::graded_state_for_fraction($ans->fraction)->is_incorrect()) {
                    $numcorrect += 1;
                }
            }
        }
        return $numcorrect;
    }

    /**
     * Return the full list of selected correct and incorrect choices.
     * @return array the full list of selected correct and incorrect choices.
     */
    public function get_new_order() {
        $correct = array();
        $incorrect = array();
        foreach ($this->answers as $ansid => $ans) {
            if ($ans->fraction > 0) {
                $correct[] = $ansid;
            } else {
                $incorrect[] = $ansid;
            }
        }
        if ($this->shuffleanswers) {
            shuffle($correct);
            shuffle($incorrect);
        }
        // Automatic random selection of answer numbers.
        if ($this->answersselectmode == 2) {
            $nbcorrect = rand(1, count($correct));
            $nbincorrect = rand(1, count($incorrect));
        } else if ($this->answersselectmode == 3) { // N random answers
            $hardsetamountofanswers = $this->hardsetamountofanswers;
            $incorrectanswerneeded = $this->hastobeoneincorrectanswer ? 1 : 0;

            // We are getting hardsetAnswersAmount-1 because at least one answer should be correct
            $maxincorrectanswers = min(count($incorrect), $hardsetamountofanswers-1);
            $nbincorrect = rand($incorrectanswerneeded, $maxincorrectanswers);
            $nbcorrect = $hardsetamountofanswers - $nbincorrect;
        } else { // Manual selection of answer numbers.
            $nbcorrect = count($correct) - $this->randomselectcorrect;
            $nbincorrect = count($incorrect) - $this->randomselectincorrect;
        }
        array_splice($correct, $nbcorrect);
        array_splice($incorrect, $nbincorrect);
        $this->order = array_merge($correct, $incorrect);
        return $this->order;
    }

}
