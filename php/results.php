<?php
require_once('config.php');
require_once('mysqli.php');

$db = new DB(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$title = 'Private Parts - Results';
$description = '';

$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : 0;
$signature = isset($_GET['signature']) ? $_GET['signature'] : '';

$query = $db->query("SELECT * FROM session WHERE session_id = '" . (int)$session_id . "'");

if (hash_equals($signature, sha1($session_id . RESULTS_KEY . $session_id)) && $query->row) {
    $valid = true;
        
    $name = $query->row['name'];
    $instagram = $query->row['instagram'];
    $facebook = $query->row['facebook'];
    
    $query = $db->query("SELECT email, COUNT(*) AS total FROM hibp_data WHERE session_id = '" . (int)$session_id . "' GROUP BY email");
    
    $breached_data = array();
    $paste_data = array();
    $email_data = array();
    $min_year = 99999;
    $max_year = 0;
    
    $breached_years = array();
    
    foreach ($query->rows as $result) {
        $email_data[$result['email']] = $result['total'];
        
        $query1 = $db->query("SELECT * FROM hibp_data WHERE session_id = '" . (int)$session_id . "' AND email = '" . $db->escape($result['email']) . "'");
        
        foreach ($query1->rows as $result1) {
            $result1['data'] = json_decode($result1['data'], true);
        
            if (!empty($result1['data']['email_count'])) {
                $paste_data[$result['email']][] = $result1;
            } else {
                $breached_data[$result['email']][] = $result1;
                
                $year = date('Y', strtotime($result1['data']['breach_date']));

                if ($year > $max_year) {
                    $max_year = $year;
                }
                
                if ($year < $min_year) {
                    $min_year = $year;
                }
                
                if (!isset($breached_years[$year])) {
                    $breached_years[$year] = 1;
                } else {
                    $breached_years[$year]++;
                }
            }
        }
    }
    
    $instagram_query = $db->query("SELECT * FROM instagram_data WHERE session_id = '" . (int)$session_id . "'");
    
    $instagram_data = array();
    
    if ($instagram_query->row) {
        $instagram_data = array(
            'image' => $instagram_query->row['image'],
            'data'  => json_decode($instagram_query->row['data'], true)
        );
    }
    
    $name_query = $db->query("SELECT url, MATCH(html) AGAINST ('" . $db->escape($name) . "') AS score FROM web_data WHERE MATCH(html) AGAINST ('" . $db->escape($name) . "') ORDER BY score DESC LIMIT 30");

    $name_data = array();
    
    foreach ($name_query->rows as $result) {
        $name_data[] = $result['url'];
    }

    if ($email_data || $instagram_data || $name_data) {
        $breached = true;
    } else {
        $breached = false;
    }
    
    // Clear data
    $db->query("DELETE FROM session WHERE session_id = '" . (int)$session_id . "'");
    $db->query("DELETE FROM instagram_data WHERE session_id = '" . (int)$session_id . "'");
    $db->query("DELETE FROM hibp_data WHERE session_id = '" . (int)$session_id . "'");
} else {
    $valid = false;
}

include_once('header.php');
?>
<div id="content">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <?php if (!$valid) { ?>
        <div class="alert alert-danger">You do not have permission to view the results. Please try again.</div>
        <?php } else { ?>
        <h1 class="mb-3">Here's your data, <?php echo $name; ?>!</h1>
        <?php if ($breached) { ?>
        <div class="alert alert-danger">We have analysed and searched based on the information you had provided. Your privacy may have been compromised!</div>
        <?php } else { ?>
        <div class="alert alert-success">We have analysed and searched based on the information you had provided. Your privacy settings looks good!</div>
        <?php } ?>
        <?php if ($email_data) { ?>
        <div class="row mt-5">
          <div class="col-12"><h4>Breached Accounts</h4></div>
          <div class="col-md-6">
            <div id="piechart" style="width:100%;height:300px;"></div>
          </div>
          <div class="col-md-6">
            <div id="linechart" style="width:100%;height:300px;"></div>
          </div>
        </div>
        <?php } ?>
        <div class="row">
          <?php foreach ($breached_data as $email => $values) { ?>
          <div class="col-12 mb-3">
            <h5><?php echo $email; ?> was found in the below incidents</h5>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <tr>
                  <th>Incident</th>
                  <th>Breached Date</th>
                  <th>Description</th>
                </tr>
                <?php foreach ($values as $result) { ?>
                <tr>
                  <td><?php echo $result['data']['title']; ?><br /><?php echo $result['data']['domain']; ?></td>
                  <td><?php echo date('d M Y', strtotime($result['data']['breach_date'])); ?></td>
                  <td><?php echo $result['data']['description']; ?></td>
                </tr>
                <?php } ?>
              </table>
            </div>
          </div>
          <?php } ?>
        </div>
        <div class="row">
          <?php foreach ($paste_data as $email => $values) { ?>
          <div class="col-12 mb-3">
            <h5><?php echo $email; ?> was found pasted at the following locations</h5>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <tr>
                  <th>Source</th>
                  <th>Date</th>
                  <th>Email Counts</th>
                </tr>
                <?php foreach ($values as $result) { ?>
                <tr>
                  <td><?php echo $result['data']['source']; ?><br /><?php echo $result['data']['title']; ?></td>
                  <td><?php echo $result['data']['date'] ? date('d M Y', strtotime($result['data']['date'])) : 'NA'; ?></td>
                  <td><?php echo $result['data']['email_count']; ?></td>
                </tr>
                <?php } ?>
              </table>
            </div>
          </div>
          <?php } ?>
        </div>
        <?php if ($instagram) { ?>
        <div class="row">
          <div class="col-12"><h4>Instagram Privacy</h4></div>
          <div class="col-12">
            <?php if ($instagram_data) { ?>
            <h5>@<?php echo $instagram; ?> Instagram account was found - <?php echo $instagram_data['data']['name']; ?></h5>
            <?php } else { ?>
            <h5>@<?php echo $instagram; ?> Instagram account was not found</h5>
            <?php } ?>
          </div>
          <?php if ($instagram_data) { ?>
          <div class="col-12">
            <div class="row">
              <div class="col-sm-6 col-md-3 mb-2">
                <a href="https://www.instagram.com/<?php echo $instagram; ?>/" target="_blank"><img src="<?php echo $instagram_data['image']; ?>" class="img-fluid center-block" /></a>
                <div class="p-2 bg-light"><?php echo $instagram_data['data']['biography']; ?></div>
              </div>
              <?php foreach ($instagram_data['data']['images'] as $image) { ?>
              <div class="col-sm-6 col-md-3 mb-2">
                <a href="<?php echo $image['url']; ?>" target="_blank"><img src="<?php echo $image['image']; ?>" class="img-fluid center-block" /></a>
                <div class="p-2 bg-light"><?php echo $image['caption']; ?></div>
              </div>
              <?php } ?>
            </div>
          </div>
          <?php } ?>
        </div>
        <?php } ?>
        <?php if ($name_data) { ?>
        <div class="row">
          <div class="col-12"><h4>Your name on websites</h4></div>
          <div class="col-12">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <tr>
                  <th>URL</th>
                </tr>
                <?php foreach ($name_data as $name_url) { ?>
                <tr>
                  <td><a href="<?php echo $name_url; ?>"><?php echo $name_url; ?></a></td>
                </tr>
                <?php } ?>
              </table>
            </div>
          </div>
        </div>
        <?php } ?>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
<?php if ($email_data) { ?>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var data = google.visualization.arrayToDataTable([
        ['Email', 'Breached Incidents Count'],
        <?php foreach ($email_data as $email => $count) { ?>
        ['<?php echo $email; ?>', <?php echo $count; ?>],
        <?php } ?>
    ]);

    var options = {
        title: 'Breached Accounts'
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));

    chart.draw(data, options);
}

google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawLine() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Year');
    data.addColumn('number', 'Breaches');

    data.addRows([
        <?php for ($i = $min_year; $i <= $max_year; $i++) { ?>
        ['<?php echo $i; ?>', <?php echo isset($breached_years[$i]) ? $breached_years[$i] : '0'; ?>],
        <?php } ?>
    ]);

    var options = {
        chart: {
            title: 'Account Breaches by Year'
        }
    };

    var chart = new google.charts.Line(document.getElementById('linechart'));

    chart.draw(data, google.charts.Line.convertOptions(options));
}

google.charts.load('current', {'packages':['line']});
google.charts.setOnLoadCallback(drawLine);
<?php } ?>
</script>
<?php
include_once('footer.php');