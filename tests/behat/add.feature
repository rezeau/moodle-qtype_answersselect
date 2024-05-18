@qtype @qtype_answersselect
Feature: Test creating a Random select answers question
  As a teacher
  In order to test my students
  I need to be able to create Random select answers question

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

  @javascript
  Scenario: Create a basic Random select answers question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Random select answers" question filling the form with:
      | Question name             | answersselect-001                            |
      | Question text             | Which of these animals are mammals?          |
      | General feedback          | The cat and the whale are mammals.|
      | Default mark              | 5                                            |
      | Shuffle the choices?      | 0                                            |
      | Number the choices?       | 1., 2., 3., ...                              |
      | Separator to be used for the right answers display | comma               |
      | Number of correct and incorrect answers | Use all answers (default mode) |
      | Choice 1                  | the cat                                      |
      | Choice 2                  | the lion                                     |
      | Choice 3                  | the shark                                    |
      | Choice 4                  | the tortoise                                 |
      | Choice 5                  | the whale                                    |
      | id_feedback_0             | yes, the cat is a mammal                     |
      | id_feedback_1             | yes, the lion is a mammal                    |
      | id_feedback_2             | no, the shark is a fish                      |
      | id_feedback_3             | nope, the tortoise is a reptile              |
      | id_feedback_4             | yes, the whale is a mammal                   |
      | id_correctanswer_0        | 1                                            |
      | id_correctanswer_1        | 1                                            |
      | id_correctanswer_2        | 0                                            |
      | id_correctanswer_3        | 0                                            |
      | id_correctanswer_4        | 1                                            |
      | Hint 1                    | First hint                                   |
      | Hint 2                    | Second hint                                  |
    Then I should see "answersselect-001"

  @javascript
  Scenario: Create a Random select answers question with optional selections of correct/incorrect answers
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Random select answers" question filling the form with:
      | Question name             | answersselect-001                            |
      | Question text             | Which of these animals are mammals?          |
      | General feedback          | The cat, the dog, the lion, etc. are mammals.|
      | Default mark              | 5                                            |
      | Shuffle the choices?      | 0                                            |
      | Number the choices?       | a., b., c., ...                              |
      | Choice 1                  | the cat                                      |
      | Choice 2                  | the lion                                     |
      | Choice 3                  | the shark                                    |
      | Choice 4                  | the tortoise                                 |
      | Choice 5                  | the whale                                    |
      | id_feedback_0             | yes, the cat is a mammal                     |
      | id_feedback_1             | yes, the lion is a mammal                    |
      | id_feedback_2             | no, the shark is a fish                      |
      | id_feedback_3             | nope, the tortoise is a reptile              |
      | id_feedback_4             | yes, the whale is a mammal                   |
      | id_correctanswer_0        | 1                                            |
      | id_correctanswer_1        | 1                                            |
      | id_correctanswer_2        | 0                                            |
      | id_correctanswer_3        | 0                                            |
      | id_correctanswer_4        | 1                                            |
      | Hint 1                    | First hint                                   |
      | Hint 2                    | Second hint                                  |
    Then I should see "answersselect-001"
    And I choose "Edit question" action for "answersselect-001" in the question bank
    And I press "id_updatebutton"
    Then I set the field "id_answersselectmode" to "1"
    Then I should see "Number of correct answers"
    And I should see "3"
    And I should see "Number of incorrect answers"
    And I should see "2"
    Then I set the field "id_answersselectmode" to "2"
    And I should see "Automatic random selection"
    Then I set the field "id_answersselectmode" to "3"
    And I should see "N random answers selection"
    And I should see "N answers in question"
    And I should see "2"
    And I should see "Add at least one incorrect answer"
