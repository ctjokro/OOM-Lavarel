<?php if($type=='like') {  ?>
<span >
    <img src="{{ URL::asset('public/img/front') }}/like.png" alt="img" />
</span>
<div class="lone_mem" id="liketext<?php echo $menu_id; ?>" ></div>
<script>
 swal({
        title: "Great!",
        text: "This food successfully added in your favorite list",
        type: "success",
        html: true
    });
</script>
<?php } else { ?>
<span >
    <img src="{{ URL::asset('public/img/front') }}/unlike.png" alt="img" />
</span>
<div class="lone_mem" id="liketext<?php echo $menu_id; ?>" ></div>
<script>
swal({
    title: "Great!",
    text: "This food successfully removed from your favorite list",
    type: "success",
    html: true
});
</script>
<?php } ?>

