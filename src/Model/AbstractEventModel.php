<?php
/** The abstract class constituting the top of the Model of the IMT2571 Assignment #1 MVC-example.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */

/** The Model classes holdng data about a collection of events. This abstract class
 * offers methods for validating model data.
 */
abstract class AbstractEventModel
{

    /** Function returning the complete event archive. Events are
     * returned in order of id.
     * @return Event[] An array of event objects indexed and ordered by their id.
     * @throws Exception
     */
    abstract public function getEventArchive();

    /** Function retrieving information about a given event in the archive.
     * @param integer $id the id of the event to be retrieved
     * @return Event|null The event matching the $id exists in the archive; null otherwise.
     * @throws Exception
     */
    abstract public function getEventById($id);

    /** Adds a new event to the archive.
     * @param $event Event The event to be added - the id of the event will be set after
     *                   successful insertion.
     */
    abstract public function addEvent($event);

    /** Modifies data related to a event in the archive.
     * @param $event Event The event data to be kept.
     * @throws InvalidArgumentException if any of the $event properties are invalid.
     */
    abstract public function modifyEvent($event);

    /** Deletes data related to a event from the archive.
     * @param $id integer The id of the event that should be removed from the archive.
     * @throws InvalidArgumentException if any of the $event properties are invalid.
    */
    abstract public function deleteEvent($id);

}
