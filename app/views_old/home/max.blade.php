<?php 
if(isset($mode) && $mode != ""){
    ?>
    <script>
            swal({
                   title: "Sorry!",
                   text: "You can only add max 4 items in favorite, please login to add more!",
                   type: "error",
                   html: true
               });
               
               $('#like<?php echo $menu_id; ?>').attr('onclick','like(<?php echo $menu_id; ?>)');
    </script>
    
    <?php
}
if($type=='like') {  ?>
<span >
    <img src="{{ URL::asset('public/img/front') }}/like.png" alt="img" />
</span>
<div class="lone_mem" id="liketext<?php echo $menu_id; ?>" ></div>

<?php } else { ?>
<span >
    <img src="{{ URL::asset('public/img/front') }}/unlike.png" alt="img" />
</span>
<div class="lone_mem" id="liketext<?php echo $menu_id; ?>" ></div>
<?php } ?>

    