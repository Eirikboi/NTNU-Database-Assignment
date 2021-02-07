<?php
/** The Model implementation of the IMT2571 Assignment #1 MVC-example, storing
  * data in the session object on the web server.
  * @author Rune Hjelsvold
  * @see http://php-html.net/tutorials/model-view-controller-in-php/
  *      The tutorial code used as basis.
  */

include_once("Event.php");
include_once("AbstractEventModel.php");

/** The SessionModel is a class for an event archive stored in the session
  * object on the web server.
  * @author Rune Hjelsvold
  * @see http://php-html.net/tutorials/model-view-controller-in-php/
  * The tutorial code used as basis.
  */
class SessionEventModel extends AbstractEventModel
{
    public function __construct()
    {
        // Create an initial event archive
        if (!isset($_SESSION['EventArchive'])) {
            // The event archive
            $_SESSION['EventArchive'] = array(
                new Event('Buddy Week Opening Concert', '2019-08-13',
                          'Featuring Kjartan Lauritzen', 1),
                new Event('Quiz Night', '2019-08-15',
                          'J.J. Confuser is quiz master', 2),
                new Event('Buddy Week Closing Concert', '2019-08-24',
                          'Featuring Kakkamadafakka', 3)
            );
            // The id counter for generating new, unique event ids
            $_SESSION['EventArchive.nextId'] = 4;
        }
    }

    /** Function returning the complete event archive. Events are returned in
      * order of id.
      * @return Event[] An array of event objects indexed and ordered by id.
      */
    public function getEventArchive()
    {
        return $_SESSION['EventArchive'];
    }

    /** Function retrieveing information about a given event in the archive.
      * @param integer $id The id of the event to be retrieved.
      * @return Event|null The event in the archive matching the $id - if it
      *         exists in the archive; null otherwise.
      */
    public function getEventById($id)
    {
        Event::verifyId($id);

        // We retrieve the requested event from the session event array. In a
        // real life implementation this will be done through a db select command
        $idx = $this->getEventIndexById($id);
        if ($idx > -1) {
            return $_SESSION['EventArchive'][$idx];
        }
        return null;
    }

    /** Adds a new event to the archive.
     * @param Event $event The event to be added - the id of the event will be set after
     *                   successful insertion.
     */
    public function addEvent($event)
    {
        // Make sure event contains valid data
        $event->verify(true);

        // Insert event in archive
        $_SESSION['EventArchive'][] = $event;

        // Assign id
        $event->id = $this->lastInsertId();
    }

    /** Modifies data related to a event in the archive.
      * @param Event $event The event data to kept.
      */
    public function modifyEvent($event)
    {
        // Make sure event contains valid data
        $event->verify();

        $idx = $this->getEventIndexById($event->id);
        if ($idx > -1) {
            $_SESSION['EventArchive'][$idx]->title = $event->title;
            $_SESSION['EventArchive'][$idx]->date = $event->date;
            $_SESSION['EventArchive'][$idx]->description = $event->description;
        }
    }

    /** Deletes data related to a event from the archive.
      * @param integer $id The id of the event that should be removed from the
      *                    archive.
      */
    public function deleteEvent($id)
    {
        // Make sure id is a valid value
        Event::verifyId($id);
        
        $idx = $this->getEventIndexById($id);
        if ($idx > -1) {
            array_splice($_SESSION['EventArchive'], $idx, 1);
        }
    }

    /** Helper function finding the location of the event in the archive array.
      * @param integer $id The id of the event to look for.
      * @return integer The index of the event in the archive array; -1 if the event is
      *                 not found in the array.
      */
    protected function getEventIndexById($id)
    {
        for ($i = 0; $i < sizeof($_SESSION['EventArchive']); $i++) {
            if ((string)$_SESSION['EventArchive'][$i]->id === $id) {
                return $i;
            }
        }
        return -1;
    }

    protected function lastInsertId()
    {
        $current = $_SESSION['EventArchive.nextId'];
        $_SESSION['EventArchive.nextId']++;
        return $current;
    }
}
