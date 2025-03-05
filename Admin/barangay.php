<?php 

require_once '../db/db.php';


// $barangays = [
//     "Balintonga",
//     "Banisilon",
//     "Burgos",
//     "Calube",
//     "Caputol",
//     "Casusan",
//     "Conat",
//     "Culpan",
//     "Dalisay",
//     "Dullan",
//     "Ibabao",
//     "Labo",
//     "Lawa-an",
//     "Lobogon",
//     "Lumbayao",
//     "Macubon",
//     "Makawa",
//     "Manamong",
//     "Matipaz",
//     "Maular",
//     "Mitazan",
//     "Mohon",
//     "Monterico",
//     "Nabuna",
//     "Ospital",
//     "Palayan",
//     "Pelong",
//     "Roxas",
//     "San Pedro",
//     "Santa Ana",
//     "Sinampongan",
//     "Taguanao",
//     "Tawi-tawi",
//     "Toril",
//     "Tubod",
//     "Tuburan",
//     "Tugaya",
//     "Zamora"
// ];
// $a = 104201001;
// foreach($barangays as $bar){


// $stmt = 'INSERT INTO refbarangay (brgyid, brgyname) VALUES (?,?)';
// $stmt = $pdo->prepare($stmt);
// $stmt->execute([$a, $bar]);

// $a++;
// }
// echo 'yes'; 

$stmt = 'SELECT * FROM refbarangay';
$stmt = $pdo->prepare($stmt);
 $stmt->execute();

 $out = $stmt->fetchAll(PDO ::FETCH_ASSOC);

 $trail = date('Y-m-d H:i:s');
 
 
foreach ( $out as $bar){


    $sql = 'INSERT INTO barangay_assessment (`barangay_id`, `last_modified`) VALUES (:bar, :date)';
    $stmt=$pdo->prepare($sql);
    $stmt->execute([
        ':bar' => $bar['brgyid'],
        ':date' => $trail
    ]);
    
}

echo 'yey';

?>