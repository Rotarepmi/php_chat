<?php
  session_start();

  // iff user isnt logged in - render login page and exit this script
  if(!isset($_SESSION['logged'])){
    header('Location: index.php');
    exit();
  }
?>
<!DOCTYPE html>
<html lang="pl">
<head>

<!-- Include metatags -->
<?php require_once "../modules/meta.php"; ?>

<link rel="stylesheet" href="css/style.min.css">

</head>
<body>

  <!-- Navgation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="/">Jakub Mandra</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="/">Home</a>
          </li>
          <li class="navbar-text">
            - Zalogowano jako <b><?php echo $_SESSION['user'] ?></b> -
          </li>
          <li class="nav-item">
            <a class="nav-link" href="scripts/logout.php">Wyloguj</a>
          </li>
        </ul>

      </div>
    </div>
  </nav> <!-- End of navigation elements -->

  <main class="container">
    <div class="row">
      <div class="chat-wrapp col-md-4 mx-auto">

        <div class="chat-header">
          Zalogowano jako <b><?php echo $_SESSION['user'] ?></b>
        </div>

        <div class='post-wrapp'>
        <?php
          // convert connection errors into exceptions
          require_once "scripts/connect.php";
          mysqli_report(MYSQLI_REPORT_STRICT);

          // error handling with try-catch
          try{
            $connection = new mysqli($host, $db_user, $db_pass, $db_name);

            // if connection fails - throw exception
            if($connection->connect_errno!=0){
              throw new Exception(mysqli_connect_errno());
            }
            else{
              // fetch data from db
              $result = $connection->query("SELECT * FROM communicates");

              if(!$result){
                throw new Exception($connection->error);
              }

              // iterate throught table and display data
              while($row = $result->fetch_assoc()){
                $id = $row[id];
                $user = $row[nick];
                $time = $row[time];
                $post = $row[mess];

                if($user == $_SESSION['user']){
                  echo "<div class='message user-me'>
                    <p class='user'>$user</p>
                    <p class='post'>$post</p>
                    <p class='time'>$time</p>
                  </div>";
                }
                else{
                  echo "<div class='message user-other'>
                  <p class='user'>$user</p>
                  <p class='post'>$post</p>
                  <p class='time'>$time</p>
                  </div>";
                }
              }

              // close connection and free memmory
              $result->close();
              $connection->close();
            }
          }
          // display exceptions
          catch(Exception $e){
            echo 'Błąd serwera.';
            echo '<br />info dev '.$e;
          }
        ?>
        </div>

        <form class="form-wrapp" method="POST" action="scripts/send.php">
          <input class="form-control sr-only" type="text" name="user" <?php echo "value=".$_SESSION['user'] ?> required>
          <input class="form-control message-form" type="text" name="post" maxlength="90" size="90" required placeholder="Wpisz wiadomość" autofocus autocomplete="off">
          <button class="sr-only btn btn-outline-success" type="submit">Wyslij</button>
        </form>
      </div>
    </div>
  </main>

  <!-- Include javasript plugins -->
  <?php require_once "../modules/plugins.php"; ?>

  <script type-"text/javascript">
  // jQuery scroll animation with duration = 0 (auto scroll after refresh)
  $('.post-wrapp').stop().animate({
    scrollTop: $('.post-wrapp')[0].scrollHeight
  }, 0);

  </script>
</body>
</html>
