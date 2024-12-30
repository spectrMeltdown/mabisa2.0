<?php


enum Barangay: string
{
  case Balintonga = "Balintonga";
  case Banisilon = "Banisilon";
  case Burgos = "Burgos";
  case Calube = "Calube";
  case Caputol = "Caputol";
  case Casusan = "Casusan";
  case Conat = "Conat";
  case Culpan = "Culpan";
  case Dalisay = "Dalisay";
  case Dullan = "Dullan";
  case Ibabao = "Ibabao";
  case Labo = "Labo";
  case Lawa_an = "Lawa-an";
  case Lobogon = "Lobogon";
  case Lumbayao = "Lumbayao";
  case Macubon = "Macubon";
  case Makawa = "Makawa";
  case Manamong = "Manamong";
  case Matipaz = "Matipaz";
  case Maular = "Maular";
  case Mitazan = "Mitazan";
  case Mohon = "Mohon";
  case Monterico = "Monterico";
  case Nabuna = "Nabuna";
  case Ospital = "Ospital";
  case Palayan = "Palayan";
  case Pelong = "Pelong";
  case Roxas = "Roxas";
  case San_Pedro = "San Pedro";
  case Santa_Ana = "Santa Ana";
  case Sinampongan = "Sinampongan";
  case Taguanao = "Taguanao";
  case Tawi_tawi = "Tawi-tawi";
  case Toril = "Toril";
  case Tubod = "Tubod";
  case Tuburan = "Tuburan";
  case Tugaya = "Tugaya";
  case Zamora = "Zamora";
}

function getBarangay($data): ?Barangay
{
  foreach (Barangay::cases() as $barangay) {
    if ($data === $barangay->value) {
      return $barangay;
    }
  }
  return null;
}