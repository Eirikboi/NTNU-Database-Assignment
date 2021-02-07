<?php
/** The View of the IMT2571 Assignment #1 MVC-example that shows the complete
  * event archive.
  * @author Rune Hjelsvold
  * @see http://php-html.net/tutorials/model-view-controller-in-php/
  *      The tutorial code used as basis.
  */

require_once('Model/Event.php');
require_once('View.php');

/** The EventArchiveView is the class that creates the page showing the complete
  * event archive.
  */
class EventArchiveView extends View
{
	/** The list of events in the archive.
	  * @var Event[]
	  */
	protected $events;

	/** The event that was inserted if the page is a response to and add
	  * operation.
	  * @var Event
	  */
	protected $newEvent;

	/** The name of the operation parameter to be included on the page.
	  * @var string
	  */
	protected $opParamName;

	/** The add operation parameter value to be used on the page.
	  * @var string
	  */
	protected $addOpName;

	/** Constructor
	  * @param Event[] $events The archive of events to be shown - in the form
	  *                 of an array of Events.
	  * @param string $opParamName The name of the parameter to used in the
	  *                query string for passing the operation to be performed.
	  * @param string $addOpName The name to be used for the add operation.
	  * @param Event $newEvent The attribute should be set to null for all
	  *               operations except for add operations. In the case of an
	  *               addOperation, the event that was just added to the archive
	  *               should be passed.
	  */
	public function __construct($events, $opParamName, $addOpName,
	                            $newEvent = null)
	{
		$this->events = $events;
		$this->newEvent = $newEvent;
		$this->opParamName = $opParamName;
		$this->addOpName = $addOpName;
	}

	/** Used by the superclass to generate page title
	  * @return string Page title to be generated.
	  */
	protected function getPageTitle()
	{
		return 'Event Archive';
	}

	/** Used by the superclass to generate page content
	  * @return string Content of page to be generated.
	  */
	protected function getPageContent()
	{
		if ($this->newEvent) {
			$content = <<<HTML
<h2>Event Successfully Added</h2>
<p id='newEvent'>
HTML;
			$content .= 'The event, ' . htmlspecialchars($this->newEvent->title) . ', written by '
			          . htmlspecialchars($this->newEvent->date) . ' was successfully added to '
					  . 'the archive and was assigned ID: <span id="newEventId">'
					  . htmlspecialchars($this->newEvent->id) . '</span>.</p>';
		} else {
			$content = '';
		}

		$content .= <<<HTML
<h2>Current Titles</h2>
<table id='eventList'>
  <thead>
	<tr><th>ID</th><th>Title</th><th>Date</th><th>Description</th></tr>
  </thead>
  <tbody>
HTML;
		if (isset($this->events)) {
			foreach ($this->events as $event) {
				$content .= '<tr id="event' . $event->id . '">'
						  . '<td><a href="index.php?id=' . $event->id . '">'
						  . $event->id . '</a></td><td>'
						  . htmlspecialchars($event->title) . '</td><td>'
						  . htmlspecialchars($event->date) . '</td><td>'
						  . htmlspecialchars($event->description) . '</td></tr>';
			}
		}

		$content .= <<<HTML
  </tbody>
</table>
<h2>New Titles</h2>
HTML;
		$content .= $this->createAddForm();

		return $content;
	}

	/** Helper function generating HTML code for the form for adding new events'
	  * to the archive
	  * @return string The HTML code to be generated.
      */
	protected function createAddForm()
	{
		return
		'<form id="addForm" action="index.php" method="post">'
		. '<input name="'.$this->opParamName.'" value="'.$this->addOpName.'" type="hidden"/>'
		. '<label for="title">Title:</label>'
		. '<input name="title" type="text" value=""/>'
		. '<label for="date">Date:</label>'
		. '<input name="date" type="date" value=""/>'
		. '<label for="description">Description:</label>'
		. '<input name="description" type="text" value=""/>'
		. '<input type="submit" value="Add new event"/>'
		. '</form>';
	}
}
