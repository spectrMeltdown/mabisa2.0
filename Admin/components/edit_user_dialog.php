<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');
session_set_cookie_params(0);
session_start();

// if(!$_SESSION['idno']){ header('location:../actions/logout.php'); }
require_once '../../dbconn.php';
$dbconn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbconn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


if (!$dbconn) {
  die("ERROR: Unable to connect to database.");
}

$date_ = date('Y-m-d');
$time_ = date('H:i:s');
$year_ = date('Y');
?>
<?php
if (isset($_POST['edit'])):
  $val = $_POST['val'];
  $query = $dbconn->prepare("SELECT * FROM account as a inner join country as b on a.country_code=b.country_code inner join region as c on a.region_code=c.region_code inner join province as d on a.province_code=d.province_code inner join city as e on a.city_code=e.city_code inner join barangay as f on a.barangay_code=f.barangay_code where a.id=?");
  $query->bindParam(1, $val);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_ASSOC)
?>
  <div class="modal-header bg-primary">
    <h5 class="modal-title text-white" id="exampleModalLabel">Edit User</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    <div class="mt-4 mb-4">
      <div id="ealert"></div>
    </div>
    <div class="row">
      <div class="col-lg-6">
        <div class="form-group">
          <label for="ecountry">Country Name</label>
          <select class="form-control" id="ecountry" onchange="onchange_country(this.value)">
            <option value="<?php echo $result['country_code'] ?>"><?php echo $result['country_name'] ?></option>
            <?php
            $query = $dbconn->prepare("SELECT * FROM country ");
            $query->execute();
            while ($row1 = $query->fetch(PDO::FETCH_ASSOC)) {
            ?>
              <option value="<?php echo $row1['country_code'] ?>"><?php echo $row1['country_name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="form-group">
          <label for="eregion">Region Name</label>
          <select class="form-control" id="eregion" onchange="onchange_region(this.value)">
            <option value="<?php echo $result['region_code'] ?>"><?php echo $result['region_name'] ?></option>
          </select>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label for="eprovince">Province Name</label>
          <select class="form-control" id="eprovince" onchange="onchange_province(this.value)">
            <option value="<?php echo $result['province_code'] ?>"><?php echo $result['province_name'] ?></option>
          </select>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label for="ecity">City/Municipality Name</label>
          <select class="form-control" id="ecity" onchange="onchange_city(this.value)">
            <option value="<?php echo $result['city_code'] ?>"><?php echo $result['city_name'] ?></option>
          </select>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label for="ebarangay">Barangay Name</label>
          <select class="form-control" id="ebarangay">
            <option value="<?php echo $result['barangay_code'] ?>"><?php echo $result['barangay_name'] ?></option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-2">
        <div class="form-group">
          <label for="eusername">Username</label>
          <input class="form-control" type="text" name="id" id="id" value="<?php echo $result['id'] ?>" hidden>
          <input class="form-control" type="text" name="eusername" id="eusername"
            value="<?php echo $result['username'] ?>">
        </div>
      </div>
      <div class="col-lg-2">
        <div class="form-group">
          <label for="epassword">Password</label>
          <input class="form-control" type="password" name="epassword" id="epassword"
            value="<?php echo $result['password'] ?>">
        </div>
      </div>
      <div class="col-lg-2">
        <div class="form-group">
          <label for="eemail">Email</label>
          <input class="form-control" type="email" name="eemail" id="eemail" value="<?php echo $result['email'] ?>">
        </div>
      </div>
      <div class="col-lg-2">
        <div class="form-group">
          <label for="ephone">Phone Number</label>
          <input class="form-control" type="text" name="ephone" id="ephone" value="<?php echo $result['mobile'] ?>"
            placeholder="ex. +639123456789" maxlength="13">
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label for="eaccount_type">Account Type</label>
          <select class="form-control" name="eaccount_type" id="eaccount_type">
            <!-- <option value="05">Country's Office</option> -->
            <!-- <option value="04">Region's Office</option> -->
            <!-- <option value="03">Province's Office</option> -->
            <!-- <option value="02">Mayor's Office</option> -->
            <option value="01">Barangay</option>
          </select>
        </div>
      </div>
    </div>

  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" onclick="updateUser()">Update</button>
  </div>
<?php endif ?>