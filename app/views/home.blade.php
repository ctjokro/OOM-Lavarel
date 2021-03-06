@extends('layout')
@section('content')

{{ HTML::style('css/front/style.css') }}
{{ HTML::style('css/front/media.css') }}


{{ HTML::script('js/common.js') }}
{{ HTML::script('js/prototype.js') }}
{{ HTML::script('js/html5.js') }}
{{ HTML::script('js/listing.js') }}
{{ HTML::script('js/jquery-latest.js') }}
{{ HTML::script('js/jquery.validate.js') }}
  <!--top part start -->
        <div id="top">
            <a href="{{url("/")}}">
                {{ HTML::image('img/front/logo.gif') }}
                </a>
            <ul>
                <li class="hover">{{ link_to('/', "Home", array('escape' => false)) }}</li>
                <li><a href="#">About us</a></li>
                <li><a href="#">Contact us</a></li>
            </ul>
        </div>
        <!--top part end -->
        <!--header start -->
        <div id="header">
            <h2><span>Information About Individual</span></h2>
            <p>Individual is a <span>free, tableless, W3C-compliant</span> web design layout by Template World. This template has been tested and proven compatible with all major browser environments and operating systems. You are free to modify the design to suit your tastes in any way you like. <br />
                We only ask you to not remove "Design by Template World" and the link  from the footer of the template.<br />
                These templates are licensed under a <span>Creative Commons Attribution 2.5 License</span>.</p>
        </div>
        <!--header end -->
        <!--body start -->
        <div id="body">
            <br class="spacer" />
            <ul class="nav">
                <li class="navLink"><a href="#" class="service">services</a></li>
                <li class="navLink"><a href="#" class="testimonial">testimonials</a></li>
                <li class="navLink"><a href="#" class="project">projects</a></li>
                <li class="navLink"><a href="#" class="privacy">privacy</a></li>
                <li class="navLinkNoBdr"><a href="#" class="moreLink">latest ideas</a></li>
            </ul>
            <!--left panel start -->
            <div id="left">
                <h2>Step towards the future</h2>
                <p class="lftText">Curabitur <span>ullamcorper quam ac turpis. Nulla sitmet er</span> lorem vitae felis semper lobortis. Aliquam 	  quis liberoqua. Praesent vulputate elementum nisl.</p>
                <p class="lftText">Curabitur <span>sed diam</span> tempor nisl convallis aliquam. Cras imperdiet nisl nec augue. Morbi tempus,    tellus sed viverra elementum, massa massa egestas neque, non sollicitudin metus nisi id tellus. Morbiisi. Cum sociis natoque    penatibus et magnis dis parturient montes, nascetur ridiculus mus</p>
                <p class="viewMore"><a href="#"></a></p>
                <div id="leftBottom">
                    <p class="top1"></p>
                    <h2></h2>
                    <p class="lftBottomText"><span>penatibus et magnis dis parturient</span>  riiculus musabit. Curabitur volutpat sem nec nisl porta  iaculis lobortis leo ved</p>
                    <p class="bot1"></p>
                </div>
                <br class="spacer" />
            </div>
            <!--left panel end -->
            <!--mid panel start -->
            <div id="mid">
                <h2>whats' new</h2>
                
                  {{ HTML::image('img/front/mid_panel_pic.gif') }}
                <h3>ipsum dolor sit amet,</h3>
                <p class="midText">consectetuer adipiscingelit. Duis interdum porttitor sapien. Vivamus variu enim sed dictum bibendum, libero   ante fermentum augue, vel pharetra leo mi nec velit. Mauris semper semper ura. Aliquam erat</p>
                <p class="midText2">venenatis dignissimnullamssa lorem, suscipit</p>
                <br class="spacer" />
            </div>
            <!--mid panel end -->
            <!--right panel start -->
            <div id="right">
                <h2 class="mem">Member Login</h2>
                <form name="memberLogin" action="#" method="post">
                    <input type="text" name="name" class="txtBox" value="-your name-" />
                    <input type="password" name="name" class="txtBox" value="-password-" />
                    {{ link_to('/user/register', 'Register here', ['escape' => false]) }}
                    <input type="submit" name="login" value="" class="login" />
                    <br class="spacer" />
                </form>
                <p class="bottom2"></p>
                <h2 class="solution">Solutions</h2>
                <ul>
                    <li><a href="#">Est lorem dignissim mi, nec faucibu</a></li>
                    <li><a href="#">Non nisl. Etiam bibendum posuere nisi.</a></li>
                    <li><a href="#">Nam ante purus, aliquet</a></li>
                    <li><a href="#">Volutpat ut, turpis. In hac habitasse</a></li>
                    <li><a href="#">platea Dictumst. Etiam turpis. Vestibu</a></li>
                    <li><a href="#">lum</a></li>
                    <li><a href="#">Risus viverra cursus</a></li>
                    <li><a href="#">Quam ac turpis. Nulla sit amet lorem vit</a></li>
                    <li class="noImg"><a href="#">Felis semper lobortis. Aliquam quis lib</a></li>
                </ul>
                <br class="spacer" />
            </div>
            <!--right panel end -->
            <!--bodyBottom start -->
            <div id="bodyBottom">
                <ul>
                    <li class="one"><a href="#">Adds by natoque penatibus</a><b>Lorem ipsum dolor sit amet</b>, conster adipising elit.uis interdum 	porttitor sapien. Vivamus varius, enim sed tum bibendum, libero ante</li>
                    <li class="two"><a href="#">Adds byimperdiet odio</a><b>elementum nisl. Curabitur sed diam tempor</b>,nisl convallis aliquam. 		 	Cras imperdiet nisl nec augue. Morbi tempus, tellus sed viverra elementum</li>
                    <li class="three"><a href="#">Adds by Curabitur sed</a> venenatis,<b>ellus erat laoreet letus, nec blandit</b>,sapien diam 	   	 	vitae nulla. Mauris ligula velit, nonummy vitae, auctor in, faucibus at, urna.</li>
                </ul>
                <br class="spacer" />
            </div>
            <!--bodyBottom end-->
            <br class="spacer" />
        </div>
        <!--body end -->
        <!--footer start -->
        <div id="footerMain">
            <div id="footer">
                <ul>
                    <li><a href="#">Home</a>|</li>
                    <li><a href="#">Services</a>|</li>
                    <li><a href="#">Testimonials</a>|</li>
                    <li><a href="#">Projects</a>|</li>
                    <li><a href="#">Privacy</a>|</li>
                    <li><a href="#">Latest Ideas</a></li>
                </ul>
                <p class="copyright">&copy;Individual. All rights reserved.</p>
            </div>
        </div>

@stop