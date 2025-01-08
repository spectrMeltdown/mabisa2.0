<div class="modal fade" id="addLocation" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="exampleModalLabel">Add User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mt-4 mb-4">
          <div id="alert"></div>
        </div>
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group">
              <label for="country">Country Name</label>
              <select class="form-control" id="country" onchange="onchange_country(this.value)">
                <option selected disabled>Select Country</option>
                <?php
                $query = $pdo->prepare("SELECT * FROM country ");
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
              <label for="region">Region Name</label>
              <select class="form-control" id="region" onchange="onchange_region(this.value)">
                <option selected disabled>Select Region</option>
              </select>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="province">Province Name</label>
              <select class="form-control" id="province" onchange="onchange_province(this.value)">
                <option selected disabled>Select Province</option>
              </select>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="city">City/Municipality Name</label>
              <select class="form-control" id="city" onchange="onchange_city(this.value)">
                <option selected disabled>Select City/Municipality</option>
              </select>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="barangay">Barangay Name</label>
              <select class="form-control" id="barangay">
                <option selected disabled>Select Barangay</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-2">
            <div class="form-group">
              <label for="username_brgy">Username</label>
              <input class="form-control" type="text" name="username_brgy" id="username_brgy">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="form-group">
              <label for="password">Password</label>
              <input class="form-control" type="password" name="password" id="password">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="form-group">
              <label for="email">Email</label>
              <input class="form-control" type="email" name="email" id="email">
            </div>
          </div>
          <div class="col-lg-2">
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input class="form-control" type="text" name="phone" id="phone" placeholder="ex. +639123456789"
                maxlength="13">
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label for="account_type">Account Type</label>
              <select class="form-control" name="account_type" id="account_type">
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
        <button type="button" class="btn btn-primary" onclick="saveUser()">Save</button>
      </div>
    </div>
  </div>
</div>

<script defer type="text/javascript">
  $('#user_dataTable').DataTable();
  //   $('#tableLocation').DataTable({
  //     responsive: {
  //     details: {
  //       type: 'column'
  //     }
  //   },
  //   columnDefs: [{
  //     className: 'control',
  //     orderable: false,
  //     targets: 0
  //   }],
  //   order: [1, 'asc']
  // });
</script>