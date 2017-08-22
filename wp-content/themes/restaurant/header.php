<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <?php wp_head();?>
</head>
<body>
<script>document.body.className += ' fade-out';</script>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><?php echo get_theme_mod('restaurant_name'); ?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div id="navbar" class="navbar-collapse collapse">
<!--            <ul class="nav navbar-nav navbar-right">-->
<!--                <li class=""><a href="#intro" data-scroll="true">Home</a></li>-->
<!--                <li class=""><a href="#our_story" data-scroll="true">Services</a></li>-->
<!--                <li class=""><a href="#our_team" data-scroll="true">Our team</a></li>-->
<!--                <li class=""><a href="#testimonials" data-scroll="testimonials">Testimonials</a></li>-->
<!--                <li class=""><a href="#contact" data-scroll="true">Contact</a></li>-->
<!--            </ul>-->

            <?php wp_nav_menu( array(
                    'menu' => 'top_menu',
                    'depth' => 2,
                    'container' => false,
                    'menu_class' => 'nav navbar-nav navbar-right',
            ));
//            ?>




        </div><!--/.nav-collapse -->
    </div>
    <!-- /.container -->
</nav>