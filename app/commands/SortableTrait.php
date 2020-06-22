<?php

trait SortableTrait {

    public function scopeSortable($query) {
        if (Input::has('s') && Input::has('o'))
            return $query->orderBy(Input::get('s'), Input::get('o'));
        else
            return $query;
    }

    public static function link_to_sorting_action($col, $title = null, $slug = "") {
        if (is_null($title)) {
            $title = str_replace('_', ' ', $col);
            $title = ucfirst($title);
        }

        $indicator = (Input::get('s') == $col ? (Input::get('o') === 'asc' ? '&uarr;' : '&darr;') : null);
        $parameters = array_merge(Input::get(), array('s' => $col, 'o' => (Input::get('o') === 'asc' ? 'desc' : 'asc')));

        // revise params
        $new_param = array();
        if ($slug)
            $new_param[0] = $slug;
        if (!empty($parameters)) {
            foreach ($parameters as $key => $val) {
                $new_param[$key] = $val;
            }
        }
        
      // print_r($new_param); exit;
        return link_to_route(Route::currentRouteName(), "$title $indicator", $new_param);
    }

}