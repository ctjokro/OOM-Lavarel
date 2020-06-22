<?php
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Payment extends Eloquent {

    //use UserTrait,
    use RemindableTrait,SortableTrait;
     protected $table = 'payments';
    

}
