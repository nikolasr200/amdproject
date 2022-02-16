<?php
/*
 * For debugging purposes
 */
//  error_reporting(E_ALL);
//  ini_set('display_errors', '1');
  
/*
 * Bootstrap has been used to demonstrate a basic layout, and a submission form.
 * In order the app to be more dynamic, the required fields from the assessments have been 
 * used as variables, to be able to test different scenarios and aspects of functionality.
 */
if(isset($_POST['city']) && isset($_POST['temperature']) && isset($_POST['phoneNumber'])){

    /*
     * Include necessary classes files
     */
    function my_autoload ($pClassName) {
        include(__DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $pClassName . ".php");
    }
    
    spl_autoload_register("my_autoload");
    /*
     * Handle of the POST on the assumption that no malicious activity will take place
     */
    $city = $_POST['city'];
    $temperature = $_POST['temperature'];
    $phoneNumber = $_POST['phoneNumber'];
    /*
     * Attempt to retrieve geolocation and weather info on the specified area
     */
    $getWeather = new WeatherInfo($city, $temperature);
    $selectedCityInfo = $getWeather->getTemperature();

    if(isset($selectedCityInfo->main->temp) && $selectedCityInfo->main->temp > $temperature) {
        $message = 'Your name and Temperature more than '. $temperature .'C. ' . $selectedCityInfo->main->temp;
    }elseif(isset($selectedCityInfo->main->temp)) {
        $message = 'Your name and Temperature less than '. $temperature .'C. ' . $selectedCityInfo->main->temp;
    }else{
        $errorMessage = 'Unable to retrieve Weather Data';
    }
    /*
     * In case message is set, flow can proceed to sending sms attempt. In case of failure to retrieve
     * weather info, there is no need to proceed to sending sms.
     */
    if(isset($message)){
        $amdSMS = new AMDcomm($phoneNumber, $message);
        $track = $amdSMS->sendSMS();
        if(isset($track->trackingId)){
            $trackId = $track->trackingId;
        }else {
            $errorMessage = 'Unable to send SMS';
        }
    }
}


  ?>  
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>AMD's Technical Assessment</title>

    <!-- Bootstrap core CSS -->
<link href="bootstrap-5.1.3-dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="bootstrap-5.1.3-dist/custom/form-validation.css" rel="stylesheet">
  </head>
  <body class="bg-light">
    
<div class="container">
  <main>
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" src="bootstrap-5.1.3-dist/bootstrap-logo.svg" alt="" width="72" height="57">
      <h2>Technical Assessment</h2>
      <p class="lead">Below is a version of the technical assessment that was assigned.  City, temperature threshold and phone number placeholders, suggest the values that should be submitted, however, it is left upon to user selection where same values will be selected, or different scenarios will be tested.</p>
    </div>

    <div class="row g-5">
      <?php if (isset($city) && isset($temperature) && isset($phoneNumber)){?>  
        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <ul class="list-group mb-3">
                  <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                      <h6 class="my-0">Selected City</h6>
                    </div>
                    <small class="text-muted"><?= $city ?></small>
                  </li>
                  <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                      <h6 class="my-0">Selected Temperature</h6>
                    </div>
                    <span class="text-muted"><?= $temperature ?></span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                      <h6 class="my-0">Selected Phone number</h6>
                    </div>
                    <span class="text-muted"><?= $phoneNumber ?></span>
                  </li>
                  <?php if(!isset($errorMessage)) { ?>
                  <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                      <h6 class="my-0">Message Sent</h6>
                    </div>
                    <span class="text-muted"><?= $message ?></span>
                  </li>
                  <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                      <h6 class="my-0">Track Id</h6>
                    </div>
                    <span class="text-muted"><?= $trackId ?></span>
                  </li>
                  <?php }else{ ?>
                  <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                      <h6 class="my-0">Error Message</h6>
                    </div>
                    <span class="text-muted"><?= $errorMessage ?></span>
                  </li>
                  <?php } ?>
                </ul>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
      <div class="col-md-7 col-lg-8">
        <h4 class="mb-3">Weather Info</h4>
        <form class="needs-validation" novalidate method="post">
          <div class="row g-3">
            <div class="col-12">
              <label for="city" class="form-label">City</label>
              <input type="text" class="form-control" id="city" name="city" placeholder="Thessaloniki" required value="<?= $city??''?>">
              <div class="invalid-feedback">
                  A valid city is required.
                </div>
            </div>
            <div class="col-12">
              <label for="temperature" class="form-label">Temperature</label>
              <input type="number" step="1" class="form-control" id="temperature" name="temperature" placeholder="20" required value="<?= $temperature??''?>">
              <div class="invalid-feedback">
                A valid temperature is required.
              </div>
            </div>
            <div class="col-12">
              <label for="phoneNumber" class="form-label">Phone number</label>
              <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="+306911111111" pattern="\+30[0-9]{10}" required value="<?= $phoneNumber??''?>">
              <div class="invalid-feedback">
                A valid phoner number is required.
              </div>
            </div>
          </div>

          <hr class="my-4">

          <button class="w-100 btn btn-primary btn-lg" type="submit">Trigger</button>
        </form>
      </div>
    </div>
  </main>

  <footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">&copy; 2022 Nikos Papanikolaou</p>
  </footer>
</div>


    <script src="bootstrap-5.1.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="bootstrap-5.1.3-dist/custom/jquery-3.6.0.min.js"></script>
    <script src="bootstrap-5.1.3-dist/custom/form-validation.js"></script>
    <?php if (isset($city) && isset($temperature) && isset($phoneNumber)){?>  
        <script type="text/javascript">
            $(window).on('load', function() {
                $('#myModal').modal('show');
            });
        </script>
    <?php } ?>
  </body>
</html>