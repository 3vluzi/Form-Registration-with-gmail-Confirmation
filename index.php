<?php
date_default_timezone_set('Asia/Jakarta');
/*
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Project     : Form Pendaftaran plus konfirmasi email.                                                                │
│ Deskripsi   : User mengisi form, sistem akan mencek apakah email sudah terdaftar atau belum.                         │
│               Jika belum terdaftar, sistem akan mengirimkan email ke pendaftar sekaligus mencatat                    │
│               datanya di database.                                                                                   │
│ Author      : Izulthea                                                                                               │
│ url         : https://izulthea.com                                                                                   │
|______________________________________________________________________________________________________________________|
--------------------------------- koneksi -------------------------------- */
$host       = 'localhost';
$user       = '';
$pass       = '';
$dbname     = '';
$charset    = 'utf8';
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);
global $pdo;
/* ------------------------------- current url ------------------------------ */
function get_the_current_url()
{
    $protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
    $base_url = $protocol . "://" . $_SERVER['HTTP_HOST'];
    $complete_url =   $base_url . $_SERVER["REQUEST_URI"];
    return $complete_url;
}
$awal = get_the_current_url();
global $awal;
/* ---------------------------------- mail ---------------------------------- */
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPDebug = SMTP::DEBUG_OFF;
$mail->Host = 'tls://smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPSecure = 'tls';
$mail->SMTPAuth = true;
/* ------------------- ambil dari email terdaftar di gmail ------------------ */
$mail->Username = 'alamat_email@gmail.com';
$mail->Password = 'password_email';
$mail->setFrom('alamat_email@gmail.com', 'NAMA_PEMGIRIM');
$mail->addReplyTo('no-replyto@alamatweb.com', 'NAMA_PEMGIRIM');
$mail->Subject = 'JUDUL EMAIL';
/* ---------------------------------- mail ---------------------------------- */
/* -------------------------------- kode unik ------------------------------- */
function kodeunik($limit)
{
    return strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit));
}
/* ---------------------------- cek email pada db --------------------------- */
function cekemail($email)
{
    global $pdo;
    $sql = "SELECT * FROM NAMA_TABLE WHERE alamat_email = '$email'";
    $q = $pdo->query($sql)->rowCount();
    if ($q > 0) {
        return "1";
    } else {
        return "0";
    }
}
/* --------------------------- masukin data ke DB --------------------------- */
function masukin_data($no_daftar, $nama, $email)
{
    global $pdo;
    global $awal;
    $tgl_daftar = date('Y-m-d');
    $sql = "INSERT INTO tbl_data (`no_daftar`,`nama`,`email`) VALUES('$no_daftar','$nama','$email')";
    $q = $pdo->query($sql);
    if ($q) {
        echo '<script>alert("Terimakasih , Data Anda Kami Terima, \nSilakan Cek InBox email anda, Jika tak ditemukan, ceklah folder spam.");</script>';
        echo '<script>window.location.replace("' . $awal . '");</script>';
    }
}
/* ------------------------------- script inti ------------------------------ */
$alamat_email   = trim($_POST['email']);
$nama           = trim($_POST['nama']);
$kode           = Kodeunik(5);
$no_daftar      = 'PMB/' . $kode . '/' . date('m') . '/' . date('Y');
if (isset($_POST['daftarkan'])) {
    /* ----------------------------- cek email dulu ----------------------------- */
    if (filter_var($alamat_email, FILTER_VALIDATE_EMAIL) !== FALSE) {
        /* ------------------------------- email valid ------------------------------ */
        $cekemail = cekemail($alamat_email);
        if ($cekemail == "0") {
            /* ------------------------------- kiri email ------------------------------- */
            $mail->addAddress($alamat_email, $nama);
            $pesan = "
                Assalamualaikum, salam sejahtera,<br>";
            $pesan .= "Terimakasih Saudara/i " . strtoupper($nama) . " Nomor pendaftaran Anda adalah: " . $no_daftar;
            $pesan .= " sudah mendaftar sebagai Peserta Penerima Program Beasiswa Penelusuran Minat dan Bakatdi Kampus _NAMA_KAMPUS_";
            $pesan .= "<br>
                <br>
                Salam, <br>
                PANITIA<br>
                _NAMA_KAMPUS_  
                ";
            $mail->msgHTML($pesan);
            if (!$mail->send()) {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
            }
            /* ------------------------- masukin data ke DB juga ------------------------ */
            masukin_data($no_daftar, $nama, $email);
            //kirimemail
            /* --------------------------------- selesai -------------------------------- */
        } else {
            echo '<script>alert("Maaf Email ' . $alamat_email . ' Sudah Terdatar \nSilakan gunakan email yang lain.");</script>';
            echo '<script>window.location.replace("' . $awal . '");</script>';
        }
    } else {
        echo '<script>alert("Maaf Email ' . $alamat_email . ' Tidak Benar");</script>';
        echo '<script>window.location.replace("' . $awal . '");</script>';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>FORM PENDAFTARAN BY IZULTHEA</title>
</head>
<body>
    <div class="container p-3">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h1>SAMPLE FORM PENDAFTARAN, CONFIRM KE EMAIL</h1>
                    <h3>By <a href="https://izulthea.com">Izulthea.com</a></h3>
                    <!-- ---------------------------- konten utama 2021-11-11 jam  07:58:38.000-05:00 ----------------------------- -->
                    <form acttion="" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i>Nama Lengkap</i></label>
                                    <input type="text" name="nama" class="form-control" id="nama" placeholder="Isikan nama" onfocus="this.placeholder = ''" onblur="this.placeholder = 'nama'" onKeyUp="this.value=removeSpaces(this.value);" required>
                                </div>
                                <div class="form-group">
                                    <label><i>Email <small>(Aktif- untuk konfirmasi)</small></i></label>
                                    <input type="email" name="alamat_email" class="form-control" id="alamat_email" placeholder="Isikan Email Aktif" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Isikan Email Aktif'" onKeyUp="this.value=removeSpaces(this.value);" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info" name="daftarkan">DAFTAR SEKARANG</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- ---------------------------- konten utama  2021-11-11 jam 07:58:38.000-05:00----------------------------- -->
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
