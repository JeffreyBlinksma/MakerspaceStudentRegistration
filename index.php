<?php

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
<!doctype html>
<html lang="nl">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registratiesysteem Studenten</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  </head>
  <body>
    <img src="logo.svg" class="img-fluid" alt="Makerspace by Da Vinci">
    <div class="row">
      <div class="col">
        <div class="container">
          <h1>Aan-/afmelden</h1>
          <?php
          // define variables and set to empty values
          $ID = "";
          $clockedIn = 0;

          if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $ID = test_input($_POST["ID"]);

            $sql = "SELECT ID, TimeIn FROM jefbli_bett2023.presence WHERE ID='".$ID."'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              $clockedIn = 1;
            } else {
              $clockedIn = 0;
            }

            if ($clockedIn == 1) {
              $sql = "DELETE FROM jefbli_bett2023.presence WHERE ID='".$ID."'";

              if ($conn->query($sql) === TRUE) {
                echo "<div class=\"alert alert-success\" role=\"alert\" id=\"successalert\">
                Afmelden gelukt.
                </div>
                <script type=\"text/javascript\">
                setTimeout(() => {
                let get = document.getElementById('successalert');
                get.style.display = 'none';
                }, 5000);
                </script>";
              } else {
                echo "<div class=\"alert alert-danger\" role=\"alert\">". $conn->error ."</div>";
              }
            } else {

            $sql = "INSERT INTO jefbli_bett2023.presence (ID, TimeIn) VALUES ('".$ID."', '".date("H:i:s")."')";
            
            if ($conn->query($sql) === TRUE) {
              echo "<div class=\"alert alert-success\" role=\"alert\" id=\"successalert\">
              Aanmelden gelukt.
              </div>
              <script type=\"text/javascript\">
              setTimeout(() => {
              let get = document.getElementById('successalert');
              get.style.display = 'none';
              }, 5000);
              </script>";
            } else {
              echo "<div class=\"alert alert-danger\" role=\"alert\">". $conn->error ."</div>";
            }
          }
          }

          function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
          }
          ?>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="mb-3">
              <label for="ID" class="form-label">OV-nummer:</label>
              <input type="text" class="form-control" id="ID" name="ID" placeholder="99012345" minlength="3" maxlength="8" autofocus autocomplete="off">
            </div>
          </form>
        </div>
      </div>
      <div class="col">
        <div class="container">
          <h1>Aanwezig</h1>
          <table class="table">
            <thead>
              <tr>
                <th scope="col">OV-nummer</th>
                <th scope="col">Aanwezig sinds</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT ID, TimeIn FROM jefbli_bett2023.presence";
              $result = $conn->query($sql);
              
              if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                  echo "<tr><th scope=\"row\">" . $row["ID"]. "</th><td>" . $row["TimeIn"]. "</td></tr>";
                }
              }
              ?>
            </tbody>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
</html>
