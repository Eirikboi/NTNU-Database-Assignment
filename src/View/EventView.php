<?php
/** The View of the IMT2571 Assignment #1 MVC-example that shows details about
  * one event.
  * @author Rune Hjelsvold
  * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
  */

require_once('EventView.php');

/** The EventView is the class that creates the page showing details about one
  * event.
  */
class EventView extends View
{
    protected $event;
    protected $opParamName;
    protected $delOpName;

    /** Constructor
      * @date Rune Hjelsvold
      * @param Book $event The event to be shown.
      * @param string $opParamName The name of the parameter to used in the
      *                            query string for passing the operation to be
      *                            performed.
      * @param string $delOpName The name to be used for the delete operation.
      * @param string $modOpName The name to be used the modify operation.
      * @see http://php-html.net/tutorials/model-view-controller-in-php/
      *      The tutorial code used as basis.
      */
    public function __construct($event, $opParamName, $delOpName, $modOpName)
    {
        $this->event = $event;
        $this->opParamName = $opParamName;
        $this->delOpName = $delOpName;
        $this->modOpName = $modOpName;
    }

    /** Used by the superclass to generate page title
      * @return string Page title.
      */
    protected function getPageTitle()
    {
        return 'Event Details';
    }

    /** Helper function generating HTML code for the form for removing events
      * from the archive
      */
    protected function createDeleteButton()
    {
        return
        '<form id="delForm" action="index.php" method="post">'
        . '<input name="' . $this->opParamName . '" value="' . $this->delOpName
        . '" type="hidden" /><input name="id" value="' . $this->event->id
        . '" type="hidden" /><input type="submit" value="Delete event record" />'
        . '</form>';
    }

    /** Helper function generating HTML code for the form for modifying event
      * data
      */
    protected function createModifyForm()
    {
        return
        '<form id="modForm" action="index.php" method="post">'
        . '<input name="' . $this->opParamName . '" value="' . $this->modOpName
        . '" type="hidden" /><input name="id" value="' . $this->event->id
        . '" type="hidden"/>Title:<br/><input name="title" type="text" value="'
        . htmlspecialchars($this->event->title) . '" /><br/>Date:<br/>'
        . '<input name="date" type="date" value="'
        . htmlspecialchars($this->event->date) . '" /><br/>Description:<br/>'
        . '<input name="description" type="text" value="'
        . htmlspecialchars($this->event->description) . '" /><br/><input '
        . 'type="submit" value="Update event record" /></form>';
    }

    /** Used by the superclass to generate page content
     */
    protected function getPageContent()
    {
        return 'ID:' . $this->event->id
               . $this->createModifyForm()
               . $this->createDeleteButton()
               . '<p><a href=index.php>Back to event list</a></p>';
    }
}
