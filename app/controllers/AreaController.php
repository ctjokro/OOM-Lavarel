<?php

class AreaController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Area Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'HomeController@showWelcome');
      |
     */

    //protected $area;
//    public function __construct(Area $area) {
//        $this->area = $area;
//    }

    public function showAdmin_index($citySlug = null) {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $query = DB::table('areas');

        if ($citySlug) {
            $cityData = DB::table('cities')
                    ->where('slug', $citySlug)
                    ->first();
            $city_id = $cityData->id;
            $query->where('city_id', '=', $city_id);
        }
        $input = Input::all();
//        echo "<pre>";
//        print_r($input);
//        exit;
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();
        // $areas = Area::where('first_name', '');

        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        if (!empty($input['from_date'])) {
            $searchByDateFrom = $input['from_date'];
        }
        if (!empty($input['to_date'])) {
            $searchByDateTo = $input['to_date'];
        }

        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('areas')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));
                    return Redirect::back()->with('success_message', 'Area(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('areas')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    return Redirect::back()->with('success_message', 'Area(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('areas')
                            ->whereIn('id', $idList)
                            ->delete();
                    return Redirect::back()->with('success_message', 'Area(s) deleted successfully');
                    break;
            }
        }

        if (isset($search_keyword) && $search_keyword != '') {
            $search_keyword = strip_tags($search_keyword);
            $separator[] = 'search_keyword:' . urlencode($search_keyword);
            $areaName = str_replace('_', '\_', $search_keyword);
            $query->where('name', 'LIKE', '%' . $search_keyword . '%');
            $search_keyword = str_replace('\_', '_', $search_keyword);
        }

        if (isset($searchByDateFrom) && $searchByDateFrom != '') {
            $separator[] = 'searchByDateFrom:' . urlencode($searchByDateFrom);
            $searchByDateFrom = str_replace('_', '\_', $searchByDateFrom);
            $searchByDate_con1 = date('Y-m-d', strtotime($searchByDateFrom));
            $query->where('created', '>=', $searchByDateFrom);
            $searchByDateFrom = str_replace('\_', '_', $searchByDateFrom);
        }

        if (isset($searchByDateTo) && $searchByDateTo != '') {
            $separator[] = 'searchByDateTo:' . urlencode($searchByDateTo);
            $searchByDateTo = str_replace('_', '\_', $searchByDateTo);
            $searchByDate_con2 = date('Y-m-d', strtotime($searchByDateTo));
            $query->where('created', '<=', $searchByDateTo);
            $searchByDateTo = str_replace('\_', '_', $searchByDateTo);
        }
//            echo $query->toSql();
//            exit;

        $separator = implode("/", $separator);
        // Get all the areas
        $areas = $query->orderBy('id', 'desc')->paginate(10);
        // Show the page

        return View::make('Areas/adminindex', compact('areas', $cityData))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo)
                        ->with('cityData', $cityData);
    }

    public function showAdmin_add($citySlug = null) {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        if ($citySlug) {
            $cityData = DB::table('cities')
                    ->where('slug', $citySlug)
                    ->first();
            $city_id = $cityData->id;
        }

        $input = Input::all();
        if (!empty($input)) {
            $name = trim($input['name']);
            $rules = array(
                'name' => 'required|unique:areas', // make sure the first name field is not empty
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/area/admin_add/' . $citySlug)->withErrors($validator)->withInput(Input::all());
            } else {


                $slug = $this->createUniqueSlug($name, 'areas');

                $saveArea = array(
                    'name' => $name,
                    'city_id' => $city_id,
                    'status' => '1',
                    'slug' => $slug,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('areas')->insert(
                        $saveArea
                );

                return Redirect::to('/admin/area/admin_index/' . $citySlug)->with('success_message', 'Area saved successfully.')->with('cityData', $cityData);
            }
        } else {
            return View::make('/Areas/admin_add')->with('cityData', $cityData);
        }
    }

    public function showAdmin_editarea($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();

        $area = DB::table('areas')
                        ->where('slug', $slug)->first();
        $area_id = $area->id;

        if ($area) {
            $cityData = DB::table('cities')
                    ->where('id', $area->city_id)
                    ->first();
            $city_id = $cityData->id;
            $citySlug = $cityData->slug;
        }

        if (!empty($input)) {
            $rules = array(
                'name' => 'required', // make sure the first name field is not empty
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {
                //die('dfdf');

                return Redirect::to('/admin/area/Admin_editarea/' . $area->slug)
                                ->withErrors($validator)->withInput(Input::all());
            } else {


                $data = array(
                    'name' => $input['name'],
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('areas')
                        ->where('id', $area_id)
                        ->update($data);


                return Redirect::to('/admin/area/admin_index/' . $citySlug)->with('success_message', 'Area updated successfully.');
            }
        } else {



            return View::make('/Areas/admin_editarea')->with('detail', $area)->with('cityData', $cityData);
        }
    }

    public function showAdmin_activearea($slug = null) {
        if (!empty($slug)) {
            DB::table('areas')
                    ->where('slug', $slug)
                    ->update(['status' => 1]);

            return Redirect::back()->with('success_message', 'Area activated successfully');
        }
    }

    public function showAdmin_deactivearea($slug = null) {
        if (!empty($slug)) {
            DB::table('areas')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Area deactivated successfully');
        }
    }

    public function showAdmin_deletearea($slug = null) {
        if (!empty($slug)) {

            $area = DB::table('areas')
                            ->where('slug', $slug)->first();
            $area_id = $area->id;

            if ($area) {
                $cityData = DB::table('cities')
                        ->where('id', $area->city_id)
                        ->first();
                $city_id = $cityData->id;
                $citySlug = $cityData->slug;
            }
            DB::table('areas')->where('slug', $slug)->delete();
            return Redirect::to('/admin/area/admin_index/' . $citySlug)->with('success_message', 'Area deleted successfully');
        } else {
            return Redirect::to('/admin/area/admin_index/' . $citySlug);
        }
    }

}
