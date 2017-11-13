<?php
  // check for post field
  if(isset($_POST['post'])){

    require_once "connect.php";

    // convert connection errors into exceptions
    mysqli_report(MYSQLI_REPORT_STRICT);

    // error handling with try-catch
    try{
      $connection = new mysqli($host, $db_user, $db_pass, $db_name);

      // throw exception if connection fails
      if($connection->connect_errno!=0){
        throw new Exception(mysqli_connect_errno());
      }
      else{
        // save current server time, and data from post
        $time = date('H:i:s', time());
        $user = $_POST['user'];
        $post = $_POST['post'];

        // insert data to db
        if($result=$connection->query("INSERT INTO communicates VALUES (NULL, '$user', '$time', '$post')")){
          // if success - render app page
          header('Location: ../app.php');
        }
        // if writing into db fails - throw exception
        else{
          throw new Exception($connection->error);
        }

        // close reports stream and connection
        mysqli_report(MYSQLI_REPORT_OFF);
        $connection->close();
      }
    }
    // display exceptions
    catch(Exception $e){
      echo 'Błąd serwera.';
      echo '<br />info dev '.$e;
    }
  }

?>
