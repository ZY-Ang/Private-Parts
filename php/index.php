<?php
require_once('config.php');

$title = BRAND_NAME . ' - CS3235';
$description = '';

$name = isset($_GET['name']) ? $_GET['name'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';
$instagram = isset($_GET['instagram']) ? $_GET['instagram'] : '';
$step = isset($_GET['step']) ? $_GET['step'] : '1';

include_once('header.php');
?>
<div id="content">
  <div class="container">
    <div class="row">
      <div id="form-data" class="offset-sm-3 offset-lg-3 col-sm-6 col-lg-6 text-center">
        <h1>How private are you?</h1>
        <p class="mb-3">Find out how much the world knows about you, keep your identity safe.</p>
        <div id="step-1" class="form-group">
          <label>1. Enter your full name.</label>
          <div class="input-group">
            <input type="text" class="form-control" name="name" value="<?php echo $name; ?>" placeholder="Your Full Name" autofocus />
            <div class="input-group-append">
              <button id="button-name" class="btn btn-primary" type="button"><i class="fa fa-caret-right"></i></button>
            </div>
          </div>
        </div>
        <div id="step-2" class="form-group"<?php echo $step < 2 ? ' style="display:none;"' : ''; ?>>
          <label>2. Enter your email address.</label>
          <div class="input-group">
            <input type="email" class="form-control" name="email" value="<?php echo $email; ?>" placeholder="Email address" />
            <div class="input-group-append">
              <button id="button-email" class="btn btn-primary" type="button" ><i class="fa fa-caret-right"></i></button>
            </div>
          </div>
        </div>
        <div id="step-3" class="form-group"<?php echo $step < 3 ? ' style="display:none;"' : ''; ?>>
          <label>3. Enter your Instagram username.</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">@</span>
            </div>
            <input type="text" class="form-control" name="instagram" value="<?php echo $instagram; ?>" placeholder="Instagram username" />
            <div class="input-group-append">
              <button id="button-instagram" class="btn btn-primary" type="button"><i class="fa fa-caret-right"></i></button>
            </div>
          </div>
          <div class="mt-2">
            <small onclick="$('#button-instagram').trigger('click');" class="skip">or skip this step</small>
          </div>
        </div>
        <div id="step-4" class="form-group"<?php echo $step < 4 ? ' style="display:none;"' : ''; ?>>
          <label>4. Authorize Facebook account.</label>
          <div>
            <a href="facebook.php" id="button-facebook" class="btn btn-facebook"><i class="fab fa-facebook-square"></i> Continue with Facebook</a>
          </div>
          <div class="mt-2">
            <small onclick="$('#button-facebook').trigger('click');" class="skip">or skip this step</small>
          </div>
        </div>
        <div id="step-5" class="form-group"<?php echo $step < 5 ? ' style="display:none;"' : ''; ?>>
          <label><input type="checkbox" name="agree" id="input-agree" /> I have read and agree to the</label> <a href="privacy.php" target="_blank">Privacy Policy</a>.
          <button type="button" id="button-show" class="btn btn-success btn-block btn-lg" disabled="disabled">SHOW ME MY DATA</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
function updateFacebookURL() {
    var url = 'facebook.php?name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&email=' + encodeURIComponent($('input[name=\'email\']').val()) + '&instagram=' + encodeURIComponent($('input[name=\'instagram\']').val());

    $('#button-facebook').prop('href', url);
}

$(document).ready(function() {
    $('#button-name').click(function() {
        $('#step-2').slideDown();
        
        $('input[name=\'email\']').focus();
    });
    
    $('input[name=\'name\']').keyup(function(e) {
        if (e.which == 13){
            $('#button-name').trigger('click');
        }
        
        updateFacebookURL();
    });
    
    $('#button-email').click(function() {
        $('#step-3').slideDown();
        
        $('input[name=\'instagram\']').focus();
    });
    
    $('input[name=\'email\']').keyup(function(e) {
        if (e.which == 13){
            $('#button-email').trigger('click');
        }
        
        updateFacebookURL();
    });
    
    $('#button-instagram').click(function() {
        $('#step-4').slideDown();
    });
    
    $('input[name=\'instagram\']').keyup(function(e) {
        if (e.which == 13){
            $('#button-instagram').trigger('click');
        }
        
        updateFacebookURL();
    });
    
    $('#button-facebook').click(function() {
        $('#step-5').slideDown();
    });
    
    $('#input-agree').on('change', function() {
        if ($(this).prop('checked') == true) {
            $('#button-show').prop('disabled', false);
        } else {
            $('#button-show').prop('disabled', true);
        }
    });
    
    $('#button-show').click(function() {
        if ($('#input-agree').prop('checked') == true) {
            $.ajax({
                url: 'api.php',
                type: 'post',
                data: $('#form-data input'),
                dataType: 'json',
                beforeSend: function() {
                    var modalLoading = '<div class="modal" id="loading-dialog" data-backdrop="static" data-keyboard="false" role="dialog">\
                        <div class="modal-dialog">\
                            <div class="modal-content">\
                                <div class="modal-header">\
                                    <h4 class="modal-title">Please wait while we fetch your data...</h4>\
                                </div>\
                                <div class="modal-body">\
                                    <div class="progress">\
                                      <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar"\
                                      aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%; height: 40px">\
                                      </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>';
                    
                    $('body').append(modalLoading);
                    
                    $('#loading-dialog').modal('show');
                },
                success: function(json) {
                    location = json['location'];
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    });
});
</script>
<?php
include_once('footer.php');