<?php

class customValidation {

    public function foo($field, $value, $parameters) {
        //return true if field value is foo
        return $value == 'foo';
    }

}