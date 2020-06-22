@section('title', 'Administrator :: '.TITLE_FOR_PAGES.'Add Restaurant')
@section('content')
<div class="clear"></div>
<div class="stade">
    <div class="wrapper">
        <?php if (!empty($pageDetail)) { ?>
            <div class="ptitle"><h1>
                    {{ $pageDetail->name; }}
                </h1>
            </div>
            <div class="pdescc">
                {{ $pageDetail->description; }}
            </div>
        <?php } ?>
    </div>
</div>
<div class="clear"></div>
@stop