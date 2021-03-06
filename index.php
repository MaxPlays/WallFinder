<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <title>Wall Finder</title>

    <meta charset="utf-8">
    <meta name="author" content="Maximilian Negedly">
    <meta name="description" content="A tool to find walls">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="css/master.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
   integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
   crossorigin=""/>

   <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
    integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
    crossorigin=""></script>
  </head>
  <body>

    <?php

      include "php/config.php";
      include "php/sql.php";

      session_start();

      $loggedin = FALSE;

      if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == TRUE){
        $loggedin = TRUE;
        $user = $_SESSION["user"];
      }else{
        $cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
        if ($cookie) {
          list ($username, $token, $mac) = explode(':', $cookie);
          if (hash_equals(hash_hmac('sha256', $username . ':' . $token, $secret_key), $mac)) {

            $stmt = $conn->prepare("SELECT userid, user FROM users WHERE user=?;");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if($stmt->num_rows > 0){
              $stmt->bind_result($id, $username);
              $stmt->fetch();
            }

            $stmt = $conn->prepare("SELECT token FROM tokens WHERE user = ?;");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();
            if($stmt->num_rows > 0){
              $stmt->bind_result($usertoken);
              while($stmt->fetch()){
                if (hash_equals($usertoken, $token)) {
                    $_SESSION["loggedin"] = TRUE;
                    $_SESSION["user"] = $username;
                    $_SESSION["id"] = $id;
                    $loggedin = TRUE;
                    break;
                }
              }
            }
          }
        }
      }

      function str_lreplace($search, $replace, $subject)
      {
          $pos = strrpos($subject, $search);

          if($pos !== false)
          {
              $subject = substr_replace($subject, $replace, $pos, strlen($search));
          }

          return $subject;
      }

      //$stmt = $conn->prepare("SELECT walls.wallid,walls.title,walls.description,walls.time,walls.lat,walls.lng,users.user FROM walls INNER JOIN users ON walls.userid = users.userid;");
      $stmt = $conn->prepare("SELECT wallid, lat, lng FROM walls;");
      $stmt->execute();
      $stmt->store_result();
      if($stmt->num_rows > 0){
        //$stmt->bind_result($wallid, $title, $description, $time, $lat, $lng, $user);
        $stmt->bind_result($wallid, $lat, $lng);
        $result = "<script>var locations = [";
        while($stmt->fetch()){
          $result .= '{id: '.$wallid.', lat: '.$lat.', lng: '.$lng.'}, ';
        }
        $result = str_lreplace(", ", "", $result)."]</script>";
        echo $result;
      }

     ?>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-ligth" id="nav">
      <a class="navbar-brand" href="#">Wall finder</a>
      <button class="navbar-toggler" id="toggle" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <?php

            if($loggedin){
              echo '<li class="nav-item"><a class="nav-link" href="#" id="nav-submit">Submit</a></li>';
            }else{
              echo '<li class="nav-item"><a class="nav-link" href="#" data-toggle="modal" data-target="#register-modal" id="nav-submit">Submit</a></li>';
            }

           ?>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Account
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown" id="login-dropdown">

              <?php

                if($loggedin){
                  echo '
                    <div class="dropdown-item">Logged in as <strong>'.$user.'</strong></div>
                    <a class="dropdown-item" href="php/logout.php" id="login-logout">Logout</a>
                  ';
                }else{
                  echo '<div class="container">
                  <form>
                  <div class="form-group">
                  <label for="login-user">Username</label>
                  <input type="text" class="form-control" id="login-user" required placeholder="Enter username">
                  </div>
                  <div class="form-group">
                  <label for="login-password">Password</label>
                  <input type="password" class="form-control" required id="login-password" placeholder="Password">
                  </div>
                  <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="login-stay">
                  <label class="form-check-label" for="login-stay">Keep me logged in</label>
                  </div>
                  </form>
                  </div>
                  <div class="container">
                  <div class="btn btn-primary" id="login-submit">
                  <span id="login-text">Login</span>
                  <span id="login-loading">
                  <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                  width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
                  <path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
                  <animateTransform attributeType="xml"
                  attributeName="transform"
                  type="rotate"
                  from="0 25 25"
                  to="360 25 25"
                  dur="0.6s"
                  repeatCount="indefinite"/>
                  </path>
                  </svg>
                  </span>
                  </div>
                  </div>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" data-toggle="modal" data-target="#register-modal">Don\'t have an account?</a>';
                }

               ?>

            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="modal" data-target="#about-modal">About</a>
          </li>
        </ul>
      </div>
    </nav>

    <div class="modal fade" id="register-modal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="register-title">Create account</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <label for="register-email">Email address</label>
                <input type="email" required class="form-control" id="register-email" placeholder="Enter email">
              </div>
              <div class="form-group">
                <label for="register-user">Username</label>
                <input type="text" class="form-control" required id="register-user" placeholder="Enter username">
              </div>
              <div class="form-group">
                <label for="register-password">Password</label>
                <input type="password" class="form-control" required id="register-password" placeholder="Password">
                <input type="password" class="form-control mt-1" required id="register-password-repeat" placeholder="Repeat password">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" id="register-submit">
              <span id="register-text">Register</span>
              <span id="register-loading">
                <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
                <path fill="#000" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
                  <animateTransform attributeType="xml"
                  attributeName="transform"
                  type="rotate"
                  from="0 25 25"
                  to="360 25 25"
                  dur="0.6s"
                  repeatCount="indefinite"/>
                </path>
              </svg>
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="register-success">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Success</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            You can now log into your account.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="login-fail">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Error</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <span id="login-fail-text"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="about-modal" tabindex="-1" role="dialog" aria-labelledby="about-modal-label" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="about-modal-label">About</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            This tool helps you find walls near you to practise wall-balls.
            <br>
            <br>
            <strong>&copy; 2019 Maximilian Negedly</strong>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="more-modal" tabindex="-1" role="dialog" aria-labelledby="more-modal-label" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="more-modal-label"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="more-description"></div>
            <br>
            <strong>Score: </strong><span id="more-score"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <?php

      if($loggedin){
        echo '<div class="alert alert-success mb-0" role="alert" id="submit-alert" style="display: none;">
          Click the map at the location of the wall you would like to submit.
        </div>';
      }

     ?>

    <div id="map" style="width: 100%; height: 500px;"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    <script src="js/master.js" charset="utf-8"></script>
    <script src="js/login.js" charset="utf-8"></script>
    <script src="js/register.js" charset="utf-8"></script>
    <script src="js/map.js" charset="utf-8"></script>

    <?php

      if($loggedin){
        echo '
        <script type="text/javascript">

        function submitting(){
          return $("#submit-alert").is(":visible");
        }

        $(window).on("resize", function () {
          if(submitting()){
            $("#map").height($(window).height() - $("#nav").height() - 42 - $("#submit-alert").height());
          }else{
            $("#map").height($(window).height() - $("#nav").height() - 16);
          }
          mymap.invalidateSize();

        }).trigger("resize");


        var marker;

        function removeMarker(){
          if(marker != null)
            marker.remove();
        }

        mymap.on("click", function(e){
            if(submitting()){
              marker = L.marker(e.latlng).addTo(mymap).bindPopup(\'<form><div class="form-group"><label for="submit-title"><strong>Title</strong></label><input type="text" class="form-control" id="submit-title" placeholder="Enter title" maxlength="40"></div><div class="form-group"><label for="submit-description"><strong>Description</strong> <small>(Optional)</small></label><textarea class="form-control" id="submit-description" placeholder="Description" rows="3" maxlength="1000"></textarea></div><div class="form-group"><label for="submit-image"><strong>Image</strong> <small>(Optional)</small></label><input type="file" class="form-control" id="submit-image"></input></div></form><button class="btn btn-primary submit-submit">Submit</button>\', {
                minWidth: 300
              }).openPopup();
              marker.on(\'popupclose\', function(e) {
                removeMarker();
              });
              $("#submit-alert").hide();
              $("#map").height($(window).height() - $("#nav").height() - 16);
            }
        });

        $("#nav-submit").click(function(){
          if(!submitting()){
            $("#submit-alert").show();
            $("#map").height($(window).height() - $("#nav").height() - 42 - $("#submit-alert").height());
          }else{
            $("#submit-alert").hide();
            $("#map").height($(window).height() - $("#nav").height() - 16);
            removeMarker();
          }
        });

        $(document).on("click", ".submit-submit", function(){
          var element = $(this);
          var title = $(this).parent().find("#submit-title").val();
          var description = $(this).parent().find("#submit-description").val();
          $(this).html("Submitting...");
          $.post("php/submit.php", {title: title, description: description, lat: marker.getLatLng().lat, lng: marker.getLatLng().lng}).done(function(data){
            if(data != "Success"){
              $("#login-fail-text").html(data);
              $("#login-fail").modal("show");
            }else{
              location.reload();
            }
          }).fail(function(){
            $("#login-fail-text").html("An error has occurred.");
            $("#login-fail").modal("show");
          }).always(function(){
            element.html("Submit");
          });
        });

        $("#toggle").click(function(){
          setTimeout(function(){
            if(submitting()){
              $("#map").height($(window).height() - $("#nav").height() - 42 - $("#submit-alert").height());
            }else{
              $("#map").height($(window).height() - $("#nav").height() - 16);
            }
            mymap.invalidateSize();
          }, 300);
        });
        </script>
        ';
      }else{
        echo '
        <script type="text/javascript">
        $(window).on("resize", function () {
            $("#map").height($(window).height() - $("#nav").height() - 16);
          mymap.invalidateSize();
        }).trigger("resize");

        $("#toggle").click(function(){
          setTimeout(function(){
            $("#map").height($(window).height() - $("#nav").height() - 16);
            mymap.invalidateSize();
          }, 300);
        });
        </script>
        ';
      }

     ?>

  </body>
</html>
