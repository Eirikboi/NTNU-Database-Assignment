<?php
/** The Model implementation of the IMT2571 Assignment #1 MVC-example, storing
* data in a MySQL database using PDO.
* @author Rune Hjelsvold
* @see http://php-html.net/tutorials/model-view-controller-in-php/
*      The tutorial code used as basis.
*/

require_once("AbstractEventModel.php");
require_once("Event.php");
require_once("dbParam.php");

/** The Model is the class holding data about a archive of events.
* @todo implement class functionality.
*/
class DBEventModel extends AbstractEventModel
{
  protected $db = null;
  //    $dsn = "mysql:host=DB"
  /**
  * @param PDO $db PDO object for the database; a new one will be created if
  *                no PDO object is passed
  * @todo Complete the implementation using PDO and a real database.
  * @throws PDOException
  */
  public function __construct($db = null)
  {
    if ($db) {
      $this->db = $db;
    } else {
      try
      {
        //creating a connection
        $this->db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
        DB_USER, DB_PWD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      }
      catch(PDOException $e)
      {
        $Error_Message=$e->getMessage();
        echo $Error_Message;
      }
    }
  }

  /** Function returning the complete list of events in the archive. Events
  * are returned in order of id.
  * @return Event[] An array of event objects indexed and ordered by id.
  * @todo Complete the implementation using PDO and a real database.
  * @throws PDOException
  */
  public function getEventArchive()
  {
    $eventList = array(); //Liste med events.
    try
    {
      $sql = 'SELECT * FROM event ORDER BY id';
      //running query in db
      $stmt = $this->db->query($sql);
      //fetch all the rows in the db
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
      {
        //creating new event with current row and saves it in an array containing events
        $event = new Event($row['title'],$row['date'],$row['description'],$row['id']);
        $eventList[] = $event;
      }
    }
    catch (PDOException $e)
    {
      $Error_Message = $e->getMessage();
      echo $Error_Message;
      echo "error in getEventArchive()\n";
    }

    return $eventList;
  }

  /** Function retrieving information about a given event in the archive.
  * @param integer $id the id of the event to be retrieved
  * @return Event|null The event matching the $id exists in the archive;
  *         null otherwise.
  * @todo Implement function using PDO and a real database.
  * @throws PDOException
  */
  public function getEventById($id)
  {
    $event = null;
    try
    {
      //creating prepared statement
      $sql = 'SELECT * FROM event WHERE id = ?';
      $stmt = $this->db->prepare($sql);
      //binding values to prepared statement
      $stmt->bindValue(1,$id,PDO::PARAM_INT);
      //execute prepared statement if id is
      if($stmt->execute())
      {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $event = new Event($row['title'],$row['date'],$row['description'],$row['id']);
        //checks if the id is valid/a number
        $event->verifyId($id);
      }
    }
    catch (PDOException $e)
    {
      $Error_Message = $e->getMessage();
      echo $Error_Message;
      echo "Error in getEventById()\n";
    }
    return $event;
  }

  /** Adds a new event to the archive.
  * @param Event $event The event to be added - the id of the event will be set after successful insertion.
  * @todo Implement function using PDO and a real database.
  * @throws PDOException
  * @throws InvalidArgumentException If event data is invalid
  */
  public function addEvent($event)
  {
    $event->verify();
    try
    {
        //SQL statement:
        $sql = 'INSERT INTO event (title, date, description) VALUES( ?, ?, ?)';
          //Prepare this statement:
          $stmt = $this->db->prepare($sql);
          //Bind relevant values from the event object to the relevant spots in the statement:
          $stmt->bindValue(1, $event->title, PDO::PARAM_STR);
          $stmt->bindValue(2, $event->date, PDO::PARAM_STR);
          $stmt->bindValue(3, $event->description, PDO::PARAM_STR);
          //Execute statement:
          $stmt->execute();

          //sets event id from -1 to last inserted
          $event->id = $this->db->lastInsertId();

          return $event;
        }
        catch (PDOException $e)
        {
          $Error_Message = $e->getMessage();
          echo $Error_Message;
        }
        catch (InvalidArgumentException $inA)
        {
          $Error_Message2 = $inA->getMessage();
          echo $Error_Message2;
          echo "Dette gikk daarlig\n";
        }

      }

      /** Modifies data related to a event in the archive.
      * @param Event $event The event data to be kept.
      * @todo Implement function using PDO and a real database.
      * @throws PDOException
      * @throws InvalidArgumentException If event data is invalid
      */
      public function modifyEvent($event)
      {
        $event->verify();
        try
        {
          $sql = 'UPDATE event SET title = ?, date = ?, description = ? WHERE id = ?';

          $stmt = $this->db->prepare($sql) ;

          $stmt->bindValue(1,$event->title,PDO::PARAM_STR);
          $stmt->bindValue(2,$event->date,PDO::PARAM_STR);
          $stmt->bindValue(3,$event->description,PDO::PARAM_STR);
          $stmt->bindValue(4,$event->id,PDO::PARAM_INT);

          $stmt->execute();
        }
        catch(PDOException $e)
        {
          $Error_Message = $e->getMessage();
          echo $Error_Message;
        }
        catch (InvalidArgumentException $inA)
        {
          $Error_Message2 = $inA->getMessage();
          echo $Error_Message2;
          echo "Dette gikk daarlig\n";
        }


      }

      /** Deletes data related to a event from the archive.
      * @param $id integer The id of the event that should be removed from the archive.
      * @todo Implement function using PDO and a real database.
      * @throws PDOException
      */
      public function deleteEvent($id)
      {
        try
        {
          Event::verifyId($id);
          $sql = 'DELETE FROM event WHERE id = ?';
          $stmt = $this->db->prepare($sql) ;
          $stmt->bindValue(1,$id,PDO::PARAM_INT);
          $stmt->execute();

        }
        catch(PDOException $e)
        {
          $Error_Message = $e->getMessage();
          echo $Error_Message;
        }

      }
    }
