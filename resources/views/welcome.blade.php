<!doctype html>
<html class="no-js" lang="en">

<head>
    <!-- ==============================================
            ===TITLE AND BASIC META TAGS======
         =============================================== \-->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Sonic</title>
    <meta name="description" content="Sonic - Resposive Coming Soon Page">
    <meta name="keywords" content="HTML,CSS,JavaScript,jQuery">
    <meta name="author" content="Pro web">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- /====================xxxxxxxxxxxxxxxx==================== -->

    <!-- ==============================================
        ===================FAVICONS=======================
         =============================================== \-->
    <link rel="icon" href="main/img/favicon-icon.png" sizes="16x16">
    <link rel="apple-touch-icon" href="main/apple-touch-icon.png">
    <!-- /====================xxxxxxxxxxxxxxxx==================== -->

    <!-- ==============================================
        ===================FONTS===========================
         =============================================== \-->
    <link rel="stylesheet" href="main/css/font-awesome.min.css">
    <!-- /Font Awesome css  4.5.0 -->
    <link rel="stylesheet" href="main/fonts/site-font/fonts.css">
    <!-- /Generate eot fonts css in Folder=fonts/site-font/fonts.css-->
    <!-- /====================xxxxxxxxxxxxxxxx==================== -->

    <!-- ==============================================
        ==================PLUGINS AND LIBS CSS===============
         =============================================== \-->
    <link rel="stylesheet" href="main/css/animate.css">
    <!-- / Animate css Version - 3.5.1 -->
    <link rel="stylesheet" href="main/css/navmenu.css">
    <!-- / navmenu css -->
    <link rel="stylesheet" href="main/css/progress.css">
    <!-- / progress css -->
    <link rel="stylesheet" href="main/css/bootstrap.min.css">
    <!-- / Bootstrap  css Version -3.3.6 -->
    <link id="colors_option" rel="stylesheet" href="main/css/colors/yellow-color.css">
    <!-- / Colors css for template main color -->
    <link rel="stylesheet" href="main/style.css">
    <!-- / Template main style css -->
    <link rel="stylesheet" href="main/css/responsive.css">
    <!-- / Template responsive css -->
    <!-- /====================xxxxxxxxxxxxxxxx==================== -->
    <!--[if lt IE 9]>
            <script src="js/vendor/html5shiv.js"></script>
        <![endif]-->
    <script src="main/js/vendor/modernizr-2.8.3.min.js"></script>
    <!-- / modernizr  Version - 2.8.3 -->
    <script src="main/js/vendor/respond.min.js"></script>
</head>

<body class="preloder_priview" onload="init()">
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="main/http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

    <!-- preloder  start/-->
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>
    <!--/ preloder  end-->

    <!-- Add your site or application content here -->

    <div class="fix header-area  welcome-section">
        <!-- =======TEMPLATHEADER WELCOME SECTIONLEMENT START==== \-->
        <!-- Welcome Background  Images start \-->
        <div class="welcome-banner" style="background:url('main/img/welcome.jpg') no-repeat scroll center center">
            <div class="welcome-overlay-center"></div>
        </div>
        <!--/ Welcome Background Images End-->
        <div class="main-page-content">
            <!-- Template Box shap start \-->
            <div class="box-shap-main">
                <div class="box-shap box-top">
                    <div class="inner-border inside-box"></div>
                </div>
                <div class="box-shap box-right">
                    <div class="inner-border inside-box"></div>
                </div>
                <div class="box-shap box-left">
                    <div class="inner-border inside-box"></div>
                </div>
                <div class="box-shap box-bottom">
                    <div class="inner-border inside-box"></div>
                </div>
            </div>
            <!--/ Template Box shap end-->
            <div class="nice-scoll main-wrapper">
                <div id="snow-effect"></div>
                <div id="snow-f" style="display:none !important"></div>
                <div class="text-center main-cotainer container">
                    <div class="content-tab-wrapper">
                        <!-- Template header start \-->
                        <header class="nav-wrapper">
                            <div class="container">
                                <!-- Template Navmenu start \-->
                                <nav class="navmenu  cl-effect-21">
                                    <ul class="text-center list-inline">
                                        <li>
                                            <a class="active" href="#home" title="Home">Home</a><span class="m-sparator"></span>
                                        </li>
                                        <li>
                                            <a class="" href="{{URL::to('/cod/login')}}" title="COD">COD</a><span class="m-sparator"></span>
                                        </li>
                                        <li>
                                            <a class="" href="{{URL::to('/cod/register')}}" title="COD Register">COD Register</a><span class="m-sparator"></span>
                                        </li>
                                        <li>
                                            <a class="" href="{{URL::to('/admin/login')}}" title="COD">Admin COD</a><span class="m-sparator"></span>
                                        </li>
                                    </ul>
                                </nav>
                                <!--/ Template Navmenu end-->
                            </div>
                        </header>
                        <!--/ Template header end-->

                        <div class="tab-pane-container">

                            <!-- ======= TEMPLATE HOME SECTION ELEMENT START==== \-->
                            <section class="tab-active" id="home">
                                <div class="content-table">
                                    <div class="animated home-content content-table-call main-content">
                                        <!-- Logo text start \-->
                                        <div class="logo">

                                            <a href=""><h2><span>S</span>onic</h2></a>

                                            <!-- Logo sparator start \-->
                                            <div class="sparator-main">
                                                <span class="sparator"></span>
                                                <span class="sparator-border"></span>
                                            </div>
                                            <!--/ Logo sparator end-->
                                        </div>
                                        <!--/ Logo text end-->

                                        <!-- Home section main text start \-->
                                        <div class="welcome-text">
                                            <h1 class="fade-text"><span>Sonic</span> is coming very soon </h1>
                                            <h1 class="fade-text"><span>please</span> Stay With us</h1>
                                        </div>
                                        <!--/ Home section main text end-->

                                        <!--Count down start \-->
                                        <div class="count-down">
                                            <div id="count-down-wrapper">
                                                <ul class="list-inline text-center">
                                                    <li class="days-count">
                                                        <h2 class="days">60</h2>
                                                        <h3 class="timeRefDays">days</h3>
                                                    </li>
                                                    <li class="hours-count">
                                                        <h2 class="hours">10</h2>
                                                        <h3 class="timeRefHours">hours</h3>
                                                    </li>
                                                    <li class="minutes-count">
                                                        <h2 class="minutes">00</h2>
                                                        <h3 class="timeRefMinutes">minutes</h3>
                                                    </li>
                                                    <li class="seconds-count">
                                                        <h2 class="seconds">00</h2>
                                                        <h3 class="timeRefSeconds">seconds</h3>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- /Count down end-->
                                        </div>

                                        <div class="welcome-subscribe-wrapper">
                                            <!-- Mail mailchimp subscription ajax form start \
                                            => Replace the form action url with your own from the MailChimp supplied embed code.
                                            => Within the action url, replace "subscribe/post" with "subscribe/post-json".
                                            -->
                                            
                                            <!--/ Mail mailchimp subscription end-->
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <!--/ TEMPLATE HOME SECTION CONTENT END-->
                            <!-- /==========xxxxxx========== -->

                            <!-- =======TEMPLATE ABOUT US SECTION ELEMENT START==== \-->
                            <section id="about">
                             
                            </section>
                            <!--/ TEMPLATE ABOUT US SECTION CONTENT END-->
                            <!-- /==========xxxxxx========== -->

                            <!-- =======TEMPLATE SERVICES SECTION ELEMENT START==== \-->
                            <section id="service">
                                
                            </section>
                            <!--/ TEMPLATE SERVICE SECTION CONTENT END-->
                            <!-- /==========xxxxxx========== -->

                            <!-- =======TEMPLATE CONTACT US SECTION ELEMENT START==== \-->
                            <section id="contact">
                               
                            </section>
                            <!--/ TEMPLATE CONTACT US SECTION CONTENT END-->
                            <!-- /==========xxxxxx========== -->
                        </div>

                        <!-- Left sidebar social icons start \-->
                        <aside class="socail-icon-warpper">
                            <div class="soical-icon">
                                <ul class="text-center">
                                    <li><a href="#"><i class="fa fa-facebook"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-twitter"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-dribbble"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-github"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-linkedin"></i></a>
                                    </li>
                                    <li><a href="#"><i class="fa fa-pinterest"></i></a>
                                    </li>
                                </ul>
                            </div>
                        </aside>
                        <!--/ Left side bar social icons end-->

                        <!-- Right sidebar social icons start \-->
                        <aside class="color-warpper">
                            <div class="color-list">
                                <ul class="text-center">
                                    <li class="defult"></li>
                                    <li class="yellow"></li>
                                    <li class="lilac"></li>
                                    <li class="green"></li>
                                    <li class="blue"></li>
                                    <li class="orange"></li>
                                </ul>
                            </div>
                        </aside>
                        <!--/ Right sidebar social icons end-->

                        <!-- Footer copyright text start \-->
                        <footer class="copyright">
                            <p>All Rights Reserved by Trax Team</p>
                        </footer>
                        <!--/ Footer copyright text end-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================
        =============Canvas snow effect js=============
         =============================================== \-->
    <script src="main/js/ThreeCanvas.js"></script>
    <script src="main/js/Snow-effect.js"></script>
    <script src="main/js/Snow-effect-active.js"></script>

    <!-- ==============================================
        =============jQuery and Plugins lib js=============
         =============================================== \-->
    <script src="main/js/vendor/jquery-2.2.4.min.js"></script>
    <!--/ Mian  Jquery js Version-2.2.4 -->
    <script src="main/js/bootstrap.min.js"></script>
    <!-- / Bootstrap  js Version -3.3.6 -->
    <script src="main/js/waypoints.min.js"></script>
    <!-- / Jquery Waypoints  js Version -2.0.3 -->
    <script src="main/js/jquery.counterup.min.js"></script>
    <!-- / Jquery counterup min  js Version -1.0 -->
    <script src="main/js/appear.js"></script>
    <!-- / Jquery appear Js -->
    <script src="main/js/ajax-mailchimp.js"></script>
    <!--/ mailchimp  Ajax js -->
    <script src="main/contract-form/form-validator.min.js"></script>
    <!--/ Contrct form validator  js -->
    <script src="main/contract-form/contact-form-script.js"></script>
    <!--/ Contrct form Ajax  js -->
    <!-- <script src="main/js/count-down.js"></script> -->
    <!--/ jquery count-down js -->
    <script src="main/js/plugins.js"></script>
    <script src="main/js/jquery.nicescroll.min.js"></script>
    <script src="main/js/main.js"></script>
    <!-- / All plugins install and other custom js -->
</body>

</html>