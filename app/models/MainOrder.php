<?php


class MainOrder extends Eloquent {

    //use UserTrait,
     use SortableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'main_order';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

}
