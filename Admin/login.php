<?php
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('Asia/Manila');

// keep this here to redirect to dashboard if already logged in 
if (!empty($_COOKIE['id'])) {
    header('location:dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $isLoginPage;
    $pathPrepend = '../';
    require 'common/head.php' ?>
    <!-- import again if you are using inline scripts -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <style type="text/css">
        @media (max-width: 768px) {
            .mv_hide {
                display: none;
                /* hide the div for mobile view */
            }
        }

        .image-container {
            text-align: center;
        }

        .image-container img {
            margin: 0 2px;
            /* Adjust the margin as needed */
        }

        /* Make text smaller */
        h2,
        h5,
        label,
        .form-control {
            font-size: 0.8em !important;
        }

        /* Reduce padding */
        .p-5 {
            padding: 1rem !important;
        }

        /* Adjust button size */
        .btn {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.8em !important;
        }

        body {
            background-color: #f0f4f9;
            background-size: cover;
            /* Adjust according to your needs */
            background-repeat: no-repeat;
            background-position: center;
            /* Center the background image */
            margin: 0;
        }
    </style>
    <script src="../js/login.js" defer></script>
    <script src="../js/util/alert.js" defer></script>
</head>

<body>
    <div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <!-- <div class="row"> -->
            <div class="col-xl-6 col-lg-8 col-md-8">
                <div class="card o-hidden border-0 shadow-lg my-10" style="border-radius: 2%; background-color:#4e73df">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 mv_hide">
                                <br>
                                <br>
                                <div class="col-lg-12 text-center" style="padding-right: 0px;"> <!-- Center the content and add padding -->
                                    <div class="p-5" style="color: white">
                                        <h2> <b style="font-size: 30px;">Vision</b></h2>
                                        <h5 class="text-justify">A strongly determined and highly trusted Department committed to capacitate and nurture local government units, public order and safety institutions to sustain peaceful, progressive, and resilient communities where people live happily.</h5>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-center" style="padding-right: 0px;"> <!-- Center the content and add padding -->
                                    <div class="p-5" style="color: white;">
                                        <h2><b style="font-size: 30px; ">Mission</b></h2>
                                        <h5 class="text-justify">The Department shall promote peace and order, ensure public safety, strengthen capability of Local Government Units through active people's participation and professionalized corps and civil servants.</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6" style="background-color: #ffffff">
                                <div class="p-5">
                                    <div class="text-center mb-3">
                                        <br>
                                        <img src="../img/index.png" width="200" height="60">
                                    </div>
                                    <div class="alert alert-danger alert-dismissible fade" role="alert" id="alert">
                                    </div>
                                    <form class="user" id="loginForm">
                                        <div class="form-group">
                                            <label for="username"><b>Username:</b></label>
                                            <input type="text" class="form-control form-control-user"
                                                id="username" name="username"
                                                placeholder="Username" autocomplete="username">
                                        </div>
                                        <div class="form-group">
                                            <label for="password"><b>Password:</b></label>
                                            <div class="d-flex">
                                                <input
                                                    max=100 type="password" id="password" name="password" class="form-control form-control-user"
                                                    id="exampleInputPassword" placeholder="Password" autocomplete="current-password">
                                                <div class="mx-1"></div>
                                                <button
                                                    type="button"
                                                    id="passEye"
                                                    class="btn btn-outline-secondary d-inline-block rounded-circle p-0"
                                                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fa fa-eye"></i>
                                                </button>

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="rememberMe" name="rememberMe">
                                                <label class="custom-control-label text-grey" for="rememberMe">Remember
                                                    Me?</label>
                                            </div>
                                        </div>
                                        <!-- dont make it type=submit, because we are using js to handle form submission -->
                                        <button type="button" id="loginBtn" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <div class="text-center">
                                        <small style="font-size: 10px;">Note: This website is for the LGU of ALORAN only.</small> <!-- Centered text -->
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>