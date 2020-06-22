<?php

class citiesController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default cities Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'HomeController@showWelcome');
      |
     */

    //protected $cities;
//    public function __construct(cities $cities) {
//        $this->cities = $cities;
//    }

    public function showAdmin_index() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $input = Input::all();
//        echo "<pre>";
//        print_r($input);
//        exit;
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();
        // $citiess = cities::where('first_name', '');
        $query = City::sortable();
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
            if (isset($input['chkRecordId'])) {
                $idList = $input['chkRecordId'];
                switch ($action) {
                    case "Activate":
                        DB::table('cities')
                                ->whereIn('id', $idList)
                                ->update(array('status' => 1));
                        return Redirect::back()->with('success_message', 'city(s) activated successfully');
                        break;
                    case "Deactivate":
                        DB::table('cities')
                                ->whereIn('id', $idList)
                                ->update(array('status' => 0));
                        return Redirect::back()->with('success_message', 'City(s) deactivated successfully');
                        break;
                    case "Delete":
                        DB::table('cities')
                                ->whereIn('id', $idList)
                                ->delete();
                        return Redirect::back()->with('success_message', 'City(s) deleted successfully');
                        break;
                }
            }
        }

        if (isset($search_keyword) && $search_keyword != '') {
            $search_keyword = strip_tags($search_keyword);
            $separator[] = 'search_keyword:' . urlencode($search_keyword);
            $cityName = str_replace('_', '\_', $search_keyword);
            $query->orwhere('name', 'LIKE', '%' . $search_keyword . '%');
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
        // Get all the cities
        $cities = $query->orderBy('name', 'asc')->sortable()->paginate(10);
        // Show the page

        return View::make('Cities/adminindex', compact('cities'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_add() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $input = Input::all();
        if (!empty($input)) {
            $name = trim($input['name']);
            $rules = array(
                'name' => 'required|unique:cities', // make sure the first name field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/cities/admin_add')->withErrors($validator)->withInput(Input::all());
            } else {

                $slug = $this->createUniqueSlug($name, 'cities');
                $saveCities = array(
                    'name' => $name,
                    'status' => '1',
                    'slug' => $slug,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('cities')->insert(
                        $saveCities
                );

                return Redirect::to('/admin/cities/admin_index')->with('success_message', 'City saved successfully.');
            }
        } else {
            return View::make('/Cities/admin_add');
        }
    }

    public function showAdmin_editcity($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();

        $city = DB::table('cities')
                        ->where('slug', $slug)->first();
        $city_id = $city->id;


        if (!empty($input)) {
            $rules = array(
                'name' => "required|unique:cities,name," . $city_id, // make sure the first name field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/cities/Admin_editcity/' . $city->slug)
                                ->withErrors($validator)->withInput(Input::all());
            } else {

                $data = array(
                    'name' => $input['name'],
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('cities')
                        ->where('id', $city_id)
                        ->update($data);

                return Redirect::to('/admin/cities/admin_index')->with('success_message', 'City updated successfully.');
            }
        } else {
            return View::make('/Cities/admin_editcity')->with('detail', $city);
        }
    }

    public function showAdmin_activecity($slug = null) {
        if (!empty($slug)) {
            DB::table('cities')
                    ->where('slug', $slug)
                    ->update(['status' => 1]);

            return Redirect::back()->with('success_message', 'City activated successfully');
        }
    }

    public function showAdmin_deactivecity($slug = null) {
        if (!empty($slug)) {
            DB::table('cities')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'City deactivated successfully');
        }
    }

    public function showAdmin_deletecity($slug = null) {
        if (!empty($slug)) {
            DB::table('cities')->where('slug', $slug)->delete();
            return Redirect::to('/admin/cities/admin_index')->with('success_message', 'City deleted successfully');
        } else {
            return Redirect::to('/admin/cities/admin_index');
        }
    }

}
