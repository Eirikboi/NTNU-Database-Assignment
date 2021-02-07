<?php
use Codeception\Util\Locator;

class EventArchiveCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // Test to verify that the event list is displayed as expected
    public function showEventListTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php');

        // Event list content
        $I->seeInTitle('Event Archive');
        $I->seeNumberOfElements('table#eventList>tbody>tr', 3);
        // Check sample event values
        $I->see('Buddy Week Opening Concert', 'tr#event1>td:nth-child(2)');
        $I->see('2019-08-15', 'tr#event2>td:nth-child(3)');
        $I->see('Featuring Kakkamadafakka', 'tr#event3>td:nth-child(4)');
        $I->seeElement('tr#event1>td:first-child>a', ['href' => 'index.php?id=1']);
        $I->seeElement('tr#event2>td:first-child>a', ['href' => 'index.php?id=2']);
        $I->seeElement('tr#event3>td:first-child>a', ['href' => 'index.php?id=3']);

        // Add new event form content
        $I->seeElement('form#addForm>input', ['name' => 'title']);
        $I->seeElement('form#addForm>input', ['name' => 'date']);
        $I->seeElement('form#addForm>input', ['name' => 'description']);
        $I->seeElement('form#addForm>input', ['type' => 'submit',
                                              'value' => 'Add new event']);
    }

    // Test to verify that the event details page is displayed as expected
    public function showEventDetailsTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php');
        $I->click('1');

        $I->seeInTitle('Event Details');
        $I->seeElement('form#modForm>input', ['name' => 'title',
                                              'value' => 'Buddy Week Opening Concert']);
        $I->seeElement('form#modForm>input', ['name' => 'date',
                                              'value' => '2019-08-13']);
        $I->seeElement('form#modForm>input', ['name' => 'description',
                                              'value' => 'Featuring Kjartan Lauritzen']);
        $I->seeLink('Back to event list','index.php');

        // Buttons for updating and deleting event information
        $I->seeElement('form#modForm>input', ['type' => 'submit',
                                              'value' => 'Update event record']);
        $I->seeElement('form#delForm>input', ['type' => 'submit',
                                              'value' => 'Delete event record']);
    }

    // Test to verify that new events can be added when all fields are set
    public function addAllFieldEventTest(AcceptanceTester $I)
    {
        $testValues = ['title' => 'Test event',
                       'date' => '2019-08-20',
                       'description' => "The event's description"];
        $this->successfulAdd($I, $testValues);
    }

    // Test to verify that new events can be added when description is blank
    public function addEmptyDescriptionEventTest(AcceptanceTester $I)
    {
        $testValues = ['title' => 'Test event w/o description',
                       'date' => '2019-08-20',
                       'description' => ''];
        $this->successfulAdd($I, $testValues);
    }

    // Test to verify that adding an event fails if title is missing
    public function addEventWithoutTitleTest(AcceptanceTester $I)
    {
        $testValues = ['title' => '',
                       'date' => '2019-08-20',
                       'description' => 'No title should fail'];
        $this->unsuccessfulAdd($I, $testValues, 'Event title is mandatory');
    }

    // Test to verify that adding an event fails if date is missing
    public function addEventWithoutDateTest(AcceptanceTester $I)
    {
        $testValues = ['title' => 'Test event',
                       'date' => '',
                       'description' => 'No date should fail'];
        $this->unsuccessfulAdd($I, $testValues, 'Event date is mandatory');
    }

    // Test to verify that adding an event fails if date is invalid
    public function addEventInvalidDateTest(AcceptanceTester $I)
    {
        $testValues = ['title' => 'Test event',
                       'date' => '2018-13-32',
                       'description' => 'Invalid date should fail'];
        $this->unsuccessfulAdd($I, $testValues,
                               'Date must be an existing one');
    }

    // Test to verify that events can be modified when all fields are set
    public function modifyAllFieldEventTest(AcceptanceTester $I)
    {
      $testValues = ['title' => 'Test event',
                     'date' => '2019-08-20',
                     'description' => "The event's description"];
      $this->successfulModify($I, $testValues);
    }

    // Test to verify that events can be modified when description is blank
    public function modifyEmptyDescriptionEventTest(AcceptanceTester $I)
    {
      $testValues = ['title' => 'Test event w/o description',
                     'date' => '2019-08-20',
                     'description' => ''];
      $this->successfulModify($I, $testValues);
    }

    // Test to verify that modifying an event fails if title is missing
    public function modifyEventWithoutTitleTest(AcceptanceTester $I)
    {
      $testValues = ['title' => '',
                     'date' => '2019-08-20',
                     'description' => 'No title should fail'];
      $this->unsuccessfulModify($I, $testValues, 'Event title is mandatory');
    }

    // Test to verify that modifying an event fails if date is missing
    public function modifyEventWithoutDateTest(AcceptanceTester $I)
    {
      $testValues = ['title' => 'Test event',
                     'date' => '',
                     'description' => 'No date should fail'];
      $this->unsuccessfulModify($I, $testValues, 'Event date is mandatory');
    }

    // Test to verify that modifying an event fails if date is invalid
    public function modifyEventInvalidDateTest(AcceptanceTester $I)
    {
      $testValues = ['title' => 'Test event',
                     'date' => '2018-13-32',
                     'description' => 'Invalid date should fail'];
      $this->unsuccessfulModify($I, $testValues,
                             'Date must be an existing one');
    }

    // Test to verify that deleting a event succeeds.
    public function deleteEventTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php?id=2');
        $I->submitForm('#delForm', []);

        // Getting event list with event #2 removed
        $I->seeInTitle('Event Archive');
        $I->seeNumberOfElements('table#eventList>tbody>tr', 2);
        $I->dontSeeElement('tr#event2');
    }

    private function successfulAdd(AcceptanceTester $I, array $testValues)
    {
        $I->amOnPage('index.php');
        $I->submitForm('#addForm', ['title' => $testValues['title'],
                                    'date' => $testValues['date'],
                                    'description' => $testValues['description']]);

        // Getting event list with new event added as ID:4
        $I->seeInTitle('Event Archive');
        $I->seeNumberOfElements('table#eventList>tbody>tr', 4);
        $I->see('ID: 4');
        $I->seeElement('tr#event4>td:first-child>a', ['href' => 'index.php?id=4']);
        $I->see($testValues['title'], 'tr#event4>td:nth-child(2)');
        $I->see($testValues['date'], 'tr#event4>td:nth-child(3)');
        $I->see($testValues['description'], 'tr#event4>td:nth-child(4)');
        $I->seeLink('4','index.php?id=4');
    }

    private function unsuccessfulAdd(AcceptanceTester $I, array $testValues,
                                     String $errorMessage)
    {
        $I->amOnPage('index.php');
        $I->submitForm('#addForm', ['title' => $testValues['title'],
                                    'date' => $testValues['date'],
                                    'description' => $testValues['description']]);

        // Getting error page with cause of failure
        $I->seeInTitle('Error Page');
        $I->see($errorMessage);
    }

    //this is a case where the data is written correct
    private function successfulModify(AcceptanceTester $I, array $testValues)
    {
      $I->amOnPage('index.php');
      //uses index 1 to test on
      $I->seeLink('1','index.php?id=1');
      $I->click('1');
      $I->amOnPage('index.php?id=1');
      //tries to modify event with test data
      $I->submitForm('#modForm', ['title' => $testValues['title'],
                                  'date' => $testValues['date'],
                                  'description' => $testValues['description']]);

      // Getting event list with new event added as ID:4
      $I->seeInTitle('Event Archive');
      $I->seeNumberOfElements('table#eventList>tbody>tr', 3);
      //$I->see('ID: 1');
    //  $I->seeElement('tr#event1>td:first-child>a', ['href' => 'index.php?id=1']);
      $I->see($testValues['title'], 'tr#event1>td:nth-child(2)');
      $I->see($testValues['date'], 'tr#event1>td:nth-child(3)');
      $I->see($testValues['description'], 'tr#event1>td:nth-child(4)');
      //$I->seeLink('1','index.php?id=1');
    }

    //this is a case where the data is written incorrect
    private function unsuccessfulModify(AcceptanceTester $I, array $testValues,
                                     String $errorMessage)
    {
        $I->amOnPage('index.php');
        //uses index 1 to test on
        $I->seeLink('1','index.php?id=1');
        $I->click('1');
        $I->amOnPage('index.php?id=1');
        //tries to modify event with test data
        $I->submitForm('#modForm', ['title' => $testValues['title'],
                                    'date' => $testValues['date'],
                                    'description' => $testValues['description']]);

        // Getting error page with cause of failure
        $I->seeInTitle('Error Page');
        $I->see($errorMessage);
    }
}
