<!-- <?php

        require_once '../db/db.php';


        $barangays = [
            "Balintonga",
            "Banisilon",
            "Burgos",
            "Calube",
            "Caputol",
            "Casusan",
            "Conat",
            "Culpan",
            "Dalisay",
            "Dullan",
            "Ibabao",
            "Labo",
            "Lawaan",
            "Lobogon",
            "Lumbayao",
            "Macubon",
            "Makawa",
            "Manamong",
            "Matipaz",
            "Maular",
            "Mitazan",
            "Mohon",
            "Monterico",
            "Nabuna",
            "Ospital",
            "Palayan",
            "Pelong",
            "Roxas",
            "SanPedro",
            "SantaAna",
            "Sinampongan",
            "Taguanao",
            "Tawitawi",
            "Toril",
            "Tubod",
            "Tuburan",
            "Tugaya",
            "Zamora"
        ];

        try {
            // $a = 104201001;
            $startNum = 9000000000;
            $userIDs = [];
            $pdo->beginTransaction();

            foreach ($barangays as $bar) {
                $pass = '@' . $bar;
                $exec = [('Brgy. ' . $bar), (strtolower($bar) . '@gmail.com'), ($startNum++), strtolower($bar), password_hash($pass, PASSWORD_BCRYPT), 24];
                // echo var_dump($exec);

                // create user
                $stmt = 'INSERT INTO users (full_name, email, mobile_num, username, password, role_id) VALUES (?,?,?,?,?,?)';
                $stmt = $pdo->prepare($stmt);
                $stmt->execute($exec);
                $userIDs[] = $pdo->lastInsertId();
                // echo $pass;
                // echo '<br>';
            }

            // get all indicators of active
            $stmt = 'SELECT i.keyctr as iid FROM maintenance_area_indicators i 
    JOIN maintenance_criteria_setup mcs ON i.keyctr = mcs.indicator_keyctr 
    JOIN maintenance_criteria_version mcv ON mcv.keyctr = mcs.version_keyctr
    ';
            $stmt = $pdo->query($stmt);
            $inds = $stmt->fetchAll();

            // get all barangay ids
            $stmt = 'SELECT brgyid as bid FROM refbarangay';
            $stmt = $pdo->query($stmt);
            $bids = $stmt->fetchAll();

            // permission insert statement
            $permStmt = 'INSERT INTO permissions (assessment_submissions_create, assessment_submissions_read, assessment_submissions_update, assessment_submissions_delete, assessment_comments_read) VALUES (?, ?, ? , ? , ?)';

            foreach ($userIDs as $uid) {
                echo $uid;
                echo '<br>';
                foreach ($bids as $bid) {
                    echo $bid['bid'];
                    echo '<br>';
                    foreach ($inds as $iid) {
                        echo $iid['iid'];
                        echo '<br>';
                        // create and get perm id
                        $permExec = $pdo->prepare($permStmt);
                        $permExec->execute(array_fill(0, 5, 1));
                        $permId = $pdo->lastInsertId();

                        // insert the user roles barangay perms
                        $urbStmt = 'INSERT INTO user_roles_barangay (user_id, barangay_id, indicator_id, permission_id) VALUES (?, ?, ? , ? )';
                        $urb = $pdo->prepare($urbStmt);
                        $urb->execute([$uid, $bid['bid'], $iid['iid'], $permId]);
                    }
                }
            }

            $pdo->commit();
            echo 'yes';
        } catch (\Throwable $th) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo $th;
        }

// $stmt = 'SELECT * FROM refbarangay';
// $stmt = $pdo->prepare($stmt);
//  $stmt->execute();

//  $out = $stmt->fetchAll(PDO ::FETCH_ASSOC);

//  $trail = date('Y-m-d H:i:s');


// foreach ( $out as $bar){


//     $sql = 'INSERT INTO barangay_assessment (`barangay_id`, `last_modified`) VALUES (:bar, :date)';
//     $stmt=$pdo->prepare($sql);
//     $stmt->execute([
//         1':bar' => $bar['brgyid'],
//         ':date' => $trail
//     ]);

// }