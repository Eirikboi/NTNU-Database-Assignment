<?php

require_once('src/Model/DBEventModel.php');

class EventArchiveTest extends \Codeception\Test\Unit
{
    /**
      * @var \UnitTester
      */
    protected $tester;
    protected $model;

    protected function _before()
    {
        $db = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
                DB_USER, DB_PWD,
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        $this->model = new DBEventModel($db);
    }

    protected function _after()
    {
    }

    // Test that all events are retrieved from the database
    public function testGetEventArchive()
    {
        $eventList = $this->model->getEventArchive();

        // Sample tests of event list contents
        $this->assertEquals(count($eventList), 3);
        $this->assertEquals($eventList[0]->id, 1);
        $this->assertEquals($eventList[0]->title, 'Buddy Week Opening Concert');
        $this->assertEquals($eventList[1]->id, 2);
        $this->assertEquals($eventList[1]->date, '2019-08-15');
        $this->assertEquals($eventList[2]->id, 3);
        $this->assertEquals($eventList[2]->description, 'Featuring Kakkamadafakka');
    }

    // Tests that information about a single event is retrieved from the database
    public function testGetEvent()
    {
        $event = $this->model->getEventById(1);

        // Sample tests of event list contents
        $this->assertEquals($event->id, 1);
        $this->assertEquals($event->title, 'Buddy Week Opening Concert');
        $this->assertEquals($event->date, '2019-08-13');
        $this->assertEquals($event->description, 'Featuring Kjartan Lauritzen');
    }

    // Tests that get event operation fails if id is not numeric
    public function testGetEventNoNumberId()
    {
        try {
            $this->model->getEventById("1'; drop table event;--");

            // Make the test break because the call should throw an exception
            $this->assertEquals("Call succeeded", "Should throw an exception");
        } catch(InvalidArgumentException $e) {
            // Do nothing since we expect the call to fail
        }
    }

    // Test to verify that new events can be added when all fields are set
    public function testAddAllFieldEvent()
    {
        $testValues = ['title' => 'Test event',
                       'date' => '2019-08-20',
                       'description' => "The event's description"];
        $this->successfulAdd($testValues);
    }

    // Test to verify that new events can be added when description is blank
    public function testAddEmptyDescriptionEvent()
    {
        $testValues = ['title' => 'Test event w/o description',
                       'date' => '2019-08-20',
                       'description' => ''];
        $this->successfulAdd($testValues);
    }

    // Test to verify that adding an event fails if title is missing
    public function testAddEventWithoutTitle()
    {
        $testValues = ['title' => '',
                       'date' => '2019-08-20',
                       'description' => 'No title should fail'];
        $this->unsuccessfulAdd($testValues, 'Event title is mandatory');
    }

    // Test to verify that adding an event fails if date is missing
    public function testAddEventWithoutDate()
    {
        $testValues = ['title' => 'Test event',
                       'date' => '',
                       'description' => 'No date should fail'];
        $this->unsuccessfulAdd($testValues, 'Event date is mandatory');
    }

    // Test to verify that adding an event fails if date is invalid
    public function testAddEventInvalidDate()
    {
        $testValues = ['title' => 'Test event',
                       'date' => '2018-13-32',
                       'description' => 'Invalid date should fail'];
        $this->unsuccessfulAdd($testValues,
                               'Date must be of format YYYY-MM-DD');
    }

    // Test to verify that events can be modified when all fields are set
    public function testModifyAllFieldEvent()
    {
      $testValues = ['title' => 'Test event',
                     'date' => '2019-08-20',
                     'description' => "The event's description"];
      $this->successfulModify($testValues);
    }

    // Test to verify that events can be modified when description is blank
    public function testModifyEmptyDescriptionEvent()
    {
      $testValues = ['title' => 'Test event w/o description',
                     'date' => '2019-08-20',
                     'description' => ''];
      $this->successfulModify($testValues);
    }

    // Test to verify that modifying an event fails if title is missing
    public function testModifyEventWithoutTitle()
    {
      $testValues = ['title' => '',
                     'date' => '2019-08-20',
                     'description' => 'No title should fail'];
      $this->unsuccessfulModify($testValues, 'Event title is mandatory');
    }

    // Test to verify that modifying an event fails if date is missing
    public function testModifyEventWithoutDate()
    {
      $testValues = ['title' => 'Test event',
                     'date' => '',
                     'description' => 'No date should fail'];
      $this->unsuccessfulModify($testValues, 'Event date is mandatory');
    }

    // Test to verify that modifying an event fails if date is missing
    public function testModifyEventInvalidDate()
    {
      $testValues = ['title' => 'Test event',
                     'date' => '2018-13-32',
                     'description' => 'Invalid date should fail'];
      $this->unsuccessfulModify($testValues,
                             'Date must be of format YYYY-MM-DD');
    }

    // Tests that an event record can be successfully deleted.
    public function testDeleteEvent()
    {
        $this->model->deleteEvent(2);
        $this->tester->seeNumRecords(2, 'event');
        // Record was successfully deleted
        $this->tester->dontSeeInDatabase('event', ['id' => 2]);
    }

    // Tests that deleting an event fails if id is not numeric
    public function testDeleteEventNoNumberId()
    {
        try {
            $this->model->deleteEvent("1'; drop table event;--");

            // Make the test break because the call should throw an exception
            $this->assertEquals("Call succeeded", "Should throw an exception");
        } catch(InvalidArgumentException $e) {
            // Do nothing since we expect the call to fail
        }
    }

    private function successfulAdd(array $testValues) {
        $event = new Event($testValues['title'], $testValues['date'], $testValues['description']);
        $this->model->addEvent($event);

        // Id was successfully assigned
        $this->assertEquals($event->id, 4);

        $this->tester->seeNumRecords(4, 'event');
        // Record was successfully inserted
        $this->tester->seeInDatabase('event', ['id' => 4,
                                              'title' => $testValues['title'],
                                              'date' => $testValues['date'],
                                              'description' => $testValues['description']]);
    }

    private function unsuccessfulAdd(array $testValues) {
        $event = new Event($testValues['title'], $testValues['date'], $testValues['description']);

        try {
            $this->model->addEvent($event);

            // Make the test break because the call should throw an exception
            $this->assertEquals("Call succeeded", "Should throw an exception");
        } catch(InvalidArgumentException $e) {
            // Do nothing since we expect the call to fail
        }
    }

    //this is a case where the data is written correct
    private function successfulModify(array $testValues) {
      
        $event = new Event($testValues['title'], $testValues['date'], $testValues['description'], 1);
        $this->model->modifyEvent($event);

        $this->tester->seeInDatabase('event', ['id' => 1,
                                          'title' => $testValues['title'],
                                          'date' => $testValues['date'],
                                          'description' => $testValues['description']]);

    }

    //this is a case where the data is written incorrect
    private function unsuccessfulModify(array $testValues) {

        $event = new Event($testValues['title'], $testValues['date'], $testValues['description'], 1);

        try {
            $this->model->modifyEvent($event);

            // Make the test break because the call should throw an exception
            $this->assertEquals("Call succeeded", "Should throw an exception");
          } catch(InvalidArgumentException $e) {
          // Do nothing since we expect the call to fail
      }


    }

}
//dette er en kommentar
