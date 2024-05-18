@qtype @qtype_answersselect @_switch_window @javascript
Feature: Preview a Random select answers question
  As a teacher
  In order to check my Random select answers questions will work for students
  I need to preview them

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype           | name                | template    |
      | Test questions   | answersselect   | answersselect 001   | mammals_two_of_four |

  Scenario: Preview a question and submit a partially correct response.
    When I am on the "answersselect 001" "core_question > preview" page logged in as teacher
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "saverestart"
    And I click on "the cat" "qtype_multichoice > Answer"
    And I click on "the shark" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "Yes, the cat is a mammal."
    And I should see "No, the shark is a fish."
    And I should see "Mark 0.50 out of 1.00"
    And I should see "Parts, but only parts, of your response are correct."

  Scenario: Preview a question and submit a correct response.
    When I am on the "answersselect 001" "core_question > preview" page logged in as teacher
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "saverestart"
    And I click on "the cat" "qtype_multichoice > Answer"
    And I click on "the whale" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "Yes, the cat is a mammal."
    And I should see "Yes, the whale is a mammal."
    And I should see "Mark 1.00 out of 1.00"
    And I should see "Well done!"
    And I should see "The cat and the whale are mammals."
    And I should see "The correct answers are: the cat, the whale"

  Scenario: Preview a question and submit a partially correct response and has partially correct feedback number.
    When I am on the "answersselect 001" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | name                                                                | answersselect 002                                  |
      | For any partially correct response                                  | Parts, but only parts, of your response are correct. |
      | For any incorrect response                                          | That is not right at all.                            |
      | id_shownumcorrect                                                   | 1                                                    |
    And I click on "#id_submitbutton" "css_element"
    And I am on the "answersselect 002" "core_question > preview" page
    # Set behaviour options
    And I set the following fields to these values:
      | behaviour | immediatefeedback |
    And I press "saverestart"
    And I click on "the cat" "qtype_multichoice > Answer"
    And I click on "the shark" "qtype_multichoice > Answer"
    And I press "Check"
    Then I should see "Yes, the cat is a mammal."
    And I should see "No, the shark is a fish."
    And I should see "Mark 0.50 out of 1.00"
    And I should see "Parts, but only parts, of your response are correct."
    And I should see "You have correctly selected one option."