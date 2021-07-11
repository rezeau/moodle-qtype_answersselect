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
 * Random select answers question type language strings.
 *
 * @package    qtype_answersselect
 * @copyright  2008 The Open University & 2021 Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['choices'] = 'Available choices';
$string['combinedcontrolnameanswersselect'] = 'check box group';
$string['correctanswer'] = 'Correct';
$string['err_correctanswerblank'] = 'You have marked this choice as correct but it is blank!';
$string['err_nonecorrect'] = 'You have not marked any choices as correct.';
$string['err_youneedmorechoices'] = 'You need to enter two or more choices.';
$string['notenoughcorrectanswers'] = 'You must select at least one correct choice';
$string['pluginname'] = 'Random select answers';
$string['pluginname_help'] = 'A multiple-choice, multiple-response question type allowing random selection of correct/incorrect answers.';
$string['pluginname_link'] = 'question/type/answersselect';
$string['pluginnameadding'] = 'Adding a Random select answers question';
$string['pluginnameediting'] = 'Editing a Random select answers question';
$string['pluginnamesummary'] = '<p>A multiple-choice, multiple-response question type with particular scoring rules.</p>
<p>Recommended if your question has many correct and incorrect answers from which "pool" a set number can be selected at runtime.</p>';
$string['toomanyoptions'] = 'You have selected too many options.';
$string['showeachanswerfeedback'] = 'Show the feedback for the selected responses.';
$string['yougotnright'] = 'You have correctly selected {$a->num} options.';
$string['yougot1right'] = 'You have correctly selected one option.';
$string['privacy:metadata'] = 'The Random select answers plugin does not store any personal data.';
$string['showstandardinstruction'] = 'Show standard instruction';
$string['showstandardinstruction_help'] = 'With this setting enabled, standard instruction will be supplied as part of the selection area (e.g. "Select one or more:"). If disabled, question authors can instead included instructions in the question content, if required.';
$string['randomselectcorrect'] = 'Number of correct answers';
$string['randomselectcorrect_help'] = 'Number of correct answers which will be displayed to the student.';
$string['randomselectincorrect'] = 'Number of incorrect answers';
$string['randomselectincorrect_help'] = 'Number of incorrect answers which will be displayed to the student.';
$string['answersselectmode'] = 'Number of correct and incorrect answers';
$string['answersselectmode_help'] = 'Select how many correct and incorrect answers will be displayed to the student. ***When you create a new question, you need to click the "Save changes and continue editing in order for those menu items to become active."';
$string['useallanswers'] = 'Use all answers (default mode)';
$string['manualselection'] = 'Manual selection';
$string['automaticselection'] = 'Automatic random selection';
