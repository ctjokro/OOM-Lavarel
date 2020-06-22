<?php if (count($errors) > 0 || Session::has('error_message')) { ?>
    <div class="alert alert-block alert-danger fade in">
        <button data-dismiss="alert" class="close close-sm" type="button">
            <i class="fa fa-times"></i>
        </button>
        <?php
        foreach ($errors->all() as $error)
            echo $error."<br/>";
        echo Session::get('error_message');
        Session::forget('error_message');
        ?>
    </div>
<?php } ?>

<?php if (Session::has('success_message')) { ?>
    <div class="alert alert-success fade in">
        <button data-dismiss="alert" class="close close-sm" type="button">
            <i class="fa fa-times"></i>
        </button>
        <?php
        echo Session::get('success_message');
        Session::forget('success_message');
        ?>
    </div>
<?php } ?>