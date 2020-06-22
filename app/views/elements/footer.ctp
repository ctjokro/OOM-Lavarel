<?php
$site_title = $this->requestAction(array('controller' => 'App', 'action' => 'getSiteConstant', 'title'));
$facebook_link = $this->requestAction(array('controller' => 'App', 'action' => 'getSiteConstant', 'facebook_link'));
$instagram_link = $this->requestAction(array('controller' => 'App', 'action' => 'getSiteConstant', 'instagram_link'));
$linkedin_link = $this->requestAction(array('controller' => 'App', 'action' => 'getSiteConstant', 'linkedin_link'));
$pintrest_link = $this->requestAction(array('controller' => 'App', 'action' => 'getSiteConstant', 'pinterest'));
$enquiry_mail = $this->requestAction(array('controller' => 'App', 'action' => 'getMailConstant', 'enquiry_mail'));
$company_name = ClassRegistry::init('Setting')->field('company_name', array('Setting.id' => 1));
?> 

<?php echo $this->Html->script('jquery/jquery.pstrength-min.1.2.js'); ?>


<?php echo $resto_slug;exit;
if($resto_slug == ''){ ?>
<footer class="footer">

    <div class="bottom_footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-sm-9">
                    <div class="footer_right">
                        <ul>
                            <?php
                            $about_us = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'about-us'));
                            if ($about_us == 1) {
                                echo '<li>' . $this->Html->link('About Us', array('controller' => 'pages', 'action' => 'staticpage', 'about-us'), array('rel' => 'nofollow')) . '</li>';
                            }

                            $saved_jobs = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'saved-jobs'));
                            if ($saved_jobs == 1) {
                                echo '<li>' . $this->Html->link('Saved Jobs', array('controller' => 'pages', 'action' => 'staticpage', 'saved-jobs'), array('rel' => 'nofollow')) . '</li>';
                            }

                            $companies = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'companies'));
                            if ($companies == 1) {
                                echo '<li>' . $this->Html->link('Companies', array('controller' => 'pages', 'action' => 'staticpage', 'companies'), array('rel' => 'nofollow')) . '</li>';
                            }

                            $career_tools = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'career-tools'));
                            if ($career_tools == 1) {
                                echo '<li>' . $this->Html->link('Career tools', array('controller' => 'pages', 'action' => 'staticpage', 'career-tools'), array('rel' => 'nofollow')) . '</li>';
                            }

                            $career_resources = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'career-resources'));
                            if ($career_resources == 1) {
                                echo '<li>' . $this->Html->link('Career Resources', array('controller' => 'pages', 'action' => 'staticpage', 'career-resources'), array('rel' => 'nofollow')) . '</li>';
                            }

                            $faq = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'faq'));
                            if ($faq == 1) {
                                echo '<li>' . $this->Html->link('FAQ', array('controller' => 'pages', 'action' => 'staticpage', 'faq'), array('rel' => 'nofollow')) . '</li>';
                            }

                            $benefits = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'benefits'));
                            if ($benefits == 1) {
                                echo '<li>' . $this->Html->link('Benefits', array('controller' => 'pages', 'action' => 'staticpage', 'benefits'), array('rel' => 'nofollow')) . '</li>';
                            }

                            $post_a_job = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'post-a-job'));
                            if ($post_a_job == 1) {
                                echo '<li>' . $this->Html->link('Post a Job', array('controller' => 'pages', 'action' => 'staticpage', 'post-a-job'), array('rel' => 'nofollow')) . '</li>';
                            }

                            $privacy_policy = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'privacy-policy'));
                            if ($privacy_policy == 1) {
                                echo '<li>' . $this->Html->link('Privacy Policy', array('controller' => 'pages', 'action' => 'staticpage', 'privacy-policy'), array('rel' => 'nofollow')) . '</li>';
                            }

                            $find_a_job = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'find-a-job'));
                            if ($find_a_job == 1) {
                                echo '<li>' . $this->Html->link('Find a Job', array('controller' => 'pages', 'action' => 'staticpage', 'find-a-job'), array('rel' => 'nofollow')) . '</li>';
                            }
                            
                            $resignation_sample = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'resignation-sample'));
                            if ($resignation_sample == 1) {
                                echo '<li>' . $this->Html->link('Resignation Sample', array('controller' => 'pages', 'action' => 'staticpage', 'resignation-sample'), array('rel' => 'nofollow')) . '</li>';
                            }
                            
                            $resume_sample = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'resume-sample'));
                            if ($resume_sample == 1) {
                                echo '<li>' . $this->Html->link('Resume Sample', array('controller' => 'pages', 'action' => 'staticpage', 'resume-sample'), array('rel' => 'nofollow')) . '</li>';
                            }
                            ?>
                            <li><?php echo $this->Html->link('Contact us', '/contact-us', array('rel' => 'nofollow')); ?></li>
                            <li><?php echo $this->Html->link('Sitemap', '/sitemap.html', array('rel' => 'nofollow')); ?></li>

                            <!--
                            <li><a href="javascript:void(0)">Saved Jobs</a></li>
                            <li><a href="javascript:void(0)">Companies</a></li>
                            <li><a href="javascript:void(0)">Career tools</a></li>
                            <li><a href="javascript:void(0)">BENEFITS</a></li>
                            <li><a href="javascript:void(0)">Post a Job</a></li>
                            <li><a href="javascript:void(0)">Find a Job</a></li>-->
                        </ul>

                    </div>


                    <div class="bottom_liks">
                        <i class="fa fa-copyright"></i><?php echo $company_name; ?> <?php echo date("Y"); ?> | All Rights Reserved | <?php
                        $term_and_condition = classregistry::init('Page')->field('status', array('Page.static_page_heading' => 'terms-and-conditions'));
                        if ($term_and_condition == 1) {
                            echo $this->Html->link('Terms and Conditions', array('controller' => 'pages', 'action' => 'staticpage', 'terms-and-conditions'), array('rel' => 'nofollow'));
                        }
                        ?>
                        <!-- | <a href="https://www.logicspice.com/" target="_blank">Web Solution Provider Company</a> -->
                    </div>

                </div>
                <div class="col-lg-3 col-sm-3">
                    <div class="social_icon">
                        <a href="<?php echo $facebook_link; ?>" target="_new"><i class="fa fa-facebook"></i></a>
                        <a href="<?php echo $instagram_link; ?>" target="_new"><i class="fa fa-instagram"></i></a>
                        <a href="<?php echo $linkedin_link; ?>" target="_new"><i class="fa fa-linkedin"></i></a>
                        <a href="<?php echo $pintrest_link; ?>" target="_new"><i class="fa fa-pinterest"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php }?>
<script>
   
    function offpop() {
        $('#loginModal').hide();
        $('.modal-backdrop').remove();
    }

    function loginpop() {
        $('#forgotpassword').hide();
        $('.modal-backdrop').remove();
         $('#loginModal').show();
    }
</script>

<!-- Login Popup -->

<div id="loginModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $site_title; ?> login</h4>
                <div id="loaderID" style="display:none; position:absolute;"><?php echo $this->Html->image("loader_large_blue.gif"); ?></div>
            </div>
            <div id="signinform">
                <?php echo $this->element("login"); ?>
            </div>
            <div class="modal-footer text-center">
                <p>Not a member as yet? <?php echo $this->Html->link('Jobseeker Register', array('controller' => 'users', 'action' => 'register', 'jobseeker'), array('rel' => 'nofollow')); ?> or  <?php echo $this->Html->link('Employer Register', array('controller' => 'users', 'action' => 'register', 'employer'), array('rel' => 'nofollow')); ?> </p>
            </div>
        </div>

    </div>
</div>



<!-- Forgot password -->

<div id="forgotpassword" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $site_title; ?> forgot password</h4>
                <div id="loaderID" style="display:none; position:absolute;"><?php echo $this->Html->image("loader_large_blue.gif"); ?></div>
            </div>
            <div id="signinform">
                <?php echo $this->element("forgotpassword"); ?>
            </div>
            <div class="modal-footer text-center">

<!--                <span>Back to <?php //echo $this->Html->link('Sign in!', array('controller' => 'users', 'action' => 'login'), array('escape' => false, 'rel' => 'nofollow'));  ?></span>-->

                <p class="fg_lk" data-toggle="modal" data-target="#forgotpassword">
                    <?php //echo $this->Html->link('Forgot your password?', array('controller' => 'users', 'action' => 'forgotPassword'), array('escape' => false,'rel'=>'nofollow')); ?>
                   Back to <a class="text-center" onclick="loginpop()">Login</a>
                </p>

            </div>
        </div>

    </div>
</div>


<!-- feedback Popup -->

<div class="feedback_popup_section">
    <div class="feedback_button"></div>

    <div class="feedback_form">
        <div id="loaderID" class="loasd_sehcf" style="display:none; position:absolute !important; top:0 !important; right:0 !important; bottom:0 !important; left:0 !important; text-align: center !important;"><?php echo $this->Html->image("loader_large_blue.gif"); ?></div>
        <div id="reportform">
            <div id="ref" class="success_msg success_lo" style="display: none;">
                <span id="report_message" class="span_text"></span>
            </div>
            <?php echo $this->element("report"); ?>

        </div>


    </div>
</div>

<!-- feedback Popup END-->

<!-- Register Popup -->

<!--<div id="registerModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Job Portal Sign Up</h4>
            </div>
            <div class="modal-body login_modal_body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3 text-right modal_lable"><b>Username</b></div>
                        <div class="col-lg-9 padding-right-30"><input type="text" name="username" placeholder="Enter User Name" class="text_input_box"/></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3 text-right modal_lable"><b>Password</b></div>
                        <div class="col-lg-9 padding-right-30"><input type="password" name="Password" placeholder="Enter Password" class="text_input_box"/></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3">&nbsp;</div>  
                        <div class="col-lg-9 col-sm-9">
                            <div class="row">
                                <div class="col-lg-6 col-sm-6">
                                    <input type="button" name="submit" value="Login" class="login_btn btn btn-success"/>
                                </div>
                                <div class="col-lg-6 col-sm-6 text-right modal_lable"><a href="#">Forgot Password</a></div>          
                            </div>  
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer text-center">
                <p>Not a member as yet? <a href="#">Register Now</a></p>
            </div>
        </div>

    </div>
</div>-->



<script type="text/javascript">
// Select dropdowns

    $("select").addClass("not_chosen");
    if ($('select').length) {

        // Traverse through all dropdowns
        $.each($('select'), function (i, val) {
            var $el = $(val);

            // If there's any dropdown with default option selected
            // give them `not_chosen` class to style appropriately
            // We assume default option to have a value of ''
            if (!$el.val()) {
                $el.addClass('not_chosen');
            }

            // Add change event handler to do the same thing,
            // i.e., adding/removing classes for proper
            // styling. Basically we're emulating placeholder
            // behaviour on select dropdowns.
            $el.on('change', function () {
                if (!$el.val())
                    $el.addClass('not_chosen');
                else
                    $el.removeClass('not_chosen');
            });

            // end of each callback
        });
    }
</script>



<script>
    $(document).ready(function () {
        $(".feedback_button").click(function () {
            $("#report_message").empty();
            $("#ref").hide();
            $(".feedback_form").slideToggle();
            $(".feedback_button").toggleClass("off");

            $(".feedback_form").toggleClass("box");
        });
    setInterval(request,6000);
    });



<?php //if(isset($_SESSION['locationid']) && $_SESSION['locationid'] > 0){ ?>
function request(){
        $.ajax({
            type: 'POST',
            url: "<?php echo HTTP_PATH; ?>/users/countJob/",
            cache: false,
            data: {},
            beforeSend: function () {

            },
            complete: function () {

            },
            success: function (result) {
               var obj = JSON.parse(result);
               if((obj.jobcount > obj.cokkiecount) || obj.viewed == 0){
                    $('#bells').html('<a href="<?php echo HTTP_PATH ?>/jobs"><i class="fa fa-bell"></i><span class="ncr">'+obj.jobcount+'</span></a>');
                }

            }
        });
    }

<?php // } ?>

</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-90280904-1', 'auto');
  ga('send', 'pageview');

</script>
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
$.src="https://v2.zopim.com/?4toXhVRHXOtCLes7sRNCMItG7HdblsBt";z.t=+new Date;$.
type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>
<script>
$zopim(function() {
  $zopim.livechat.button.setColor('#074376');
  $zopim.livechat.window.hide();
});
</script>