<?php
include 'credentials.php';
// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "DELETE FROM jefbli_bett2023.presence where DateTimeIn < now() - interval 28 DAY;";
$conn->query($sql);
?>
<!doctype html>
<html lang="nl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registratiesysteem Studenten</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <style>
    body {
      background-image: url("https://davinci.osiris-student.nl/assets/img/osiris-background.png");
      height: 100%;
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
    }
  </style>
  <script src="https://kit.fontawesome.com/580aaae3d5.js" crossorigin="anonymous"></script>
</head>

<body>
  <div class="container p-4 my-5 bg-body rounded-5">
    <img src="logo.svg" class="img-fluid mx-auto d-block pb-2" alt="Makerspace by Da Vinci">
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

            $sql = "SELECT ID, DateTimeIn, DateTimeOut FROM jefbli_bett2023.presence WHERE ID='" . $ID . "' ORDER BY DateTimeIn DESC LIMIT 1";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                if($row["DateTimeOut"] == NULL) {
                  $clockedIn = 1;
                }
              }

            if ($clockedIn == 1) {
              $sql = "UPDATE jefbli_bett2023.presence SET DateTimeOut='" . date("Y-m-d H:i:s") . "' WHERE ID='" . $ID . "' ORDER BY DateTimeIn DESC LIMIT 1";

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
                echo "<div class=\"alert alert-danger\" role=\"alert\">" . $conn->error . "</div>";
              }
            } else {

              $sql = "INSERT INTO jefbli_bett2023.presence (ID, DateTimeIn) VALUES ('" . $ID . "', '" . date("Y-m-d H:i:s") . "')";

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
                echo "<div class=\"alert alert-danger\" role=\"alert\">" . $conn->error . "</div>";
              }
            }
          }

          function test_input($data)
          {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
          }
          ?>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
        </div>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">OV-nummer</th>
              <th scope="col">Aanwezig sinds</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "SELECT ID, DateTimeIn FROM jefbli_bett2023.presence WHERE DateTimeOut IS NULL";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              // output data of each row
              while ($row = $result->fetch_assoc()) {
                echo "<tr><th scope=\"row\">" . $row["ID"] . "</th><td>" . $row["DateTimeIn"] . "</td></tr>";
              }
            }
            ?>
          </tbody>
        </table>
        <form action="export.php">
          <button class="btn btn-success"><i class="fa-solid fa-file-excel"></i> Exporteer naar Excel</button>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>
