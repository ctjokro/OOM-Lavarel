<?php


use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class Cuisine extends Eloquent implements RemindableInterface {

	use RemindableTrait, SortableTrait;
         

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'cuisines';


}
