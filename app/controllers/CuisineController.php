<?php

class CuisineController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Cuisine Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'HomeController@showWelcome');
      |
     */

    //protected $cuisine;
//    public function __construct(Cuisine $cuisine) {
//        $this->cuisine = $cuisine;
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
        // $cuisines = Cuisine::where('first_name', '');
     //   $query = DB::table('cuisines');
         $query = Cuisine::sortable();
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
                    DB::table('cuisines')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));
                    return Redirect::back()->with('success_message', 'Cuisine(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('cuisines')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    return Redirect::back()->with('success_message', 'Cuisine(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('cuisines')
                            ->whereIn('id', $idList)
                            ->delete();
                    return Redirect::back()->with('success_message', 'Cuisine(s) deleted successfully');
                    break;
            }
        }

        if (isset($search_keyword) && $search_keyword != '') {
            $search_keyword = strip_tags($search_keyword);
            $separator[] = 'search_keyword:' . urlencode($search_keyword);
            $cuisineName = str_replace('_', '\_', $search_keyword);
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
        // Get all the cuisines
        $cuisines = $query->orderBy('id', 'desc')->sortable()->paginate(10);
        // Show the page

        return View::make('Cuisines/adminindex', compact('cuisines'))->with('search_keyword', $search_keyword)
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
                'name' => 'required|unique:cuisines', // make sure the first name field is not empty
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/cuisine/admin_add')->withErrors($validator)->withInput(Input::all());
            } else {


                $slug = $this->createUniqueSlug($name, 'cuisines');
                $saveCuisine = array(
                    'name' => $name,
                    'status' => '1',
                    'slug' => $slug,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('cuisines')->insert(
                        $saveCuisine
                );

                return Redirect::to('/admin/cuisine/admin_index')->with('success_message', 'Cuisine saved successfully.');
            }
        } else {
            return View::make('/Cuisines/admin_add');
        }
    }

    public function showAdmin_editcuisine($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();

        $cuisine = DB::table('cuisines')
                        ->where('slug', $slug)->first();

        if (empty($cuisine))
            return Redirect::to('/admin/cuisine/admin_index');
        
        $cuisine_id = $cuisine->id;


        if (!empty($input)) {
            $rules = array(
                'name' => 'required|unique:cuisines,name,' . $cuisine_id, // make sure the first name field is not empty
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/cuisine/Admin_editcuisine/' . $cuisine->slug)
                                ->withErrors($validator)->withInput(Input::all());
            } else {

                $data = array(
                    'name' => $input['name'],
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('cuisines')
                        ->where('id', $cuisine_id)
                        ->update($data);


                return Redirect::to('/admin/cuisine/admin_index')->with('success_message', 'Cuisine updated successfully.');
            }
        } else {



            return View::make('/Cuisines/admin_editcuisine')->with('detail', $cuisine);
        }
    }

    public function showAdmin_activecuisine($slug = null) {
        if (!empty($slug)) {
            DB::table('cuisines')
                    ->where('slug', $slug)
                    ->update(['status' => 1]);
            
            $cuisine = DB::table('cuisines')
                        ->where('slug', $slug)->first();
            $cuisine_id = $cuisine->id;
          
            DB::table('menu_item')
                    ->where('cuisines_id', $cuisine_id)
                    ->update(['status' => 1]);

            return Redirect::back()->with('success_message', 'Cuisine activated successfully');
        }
    }

    public function showAdmin_deactivecuisine($slug = null) {
         $cuisine = DB::table('cuisines')
                        ->where('slug', $slug)->first();
          $cuisine_id = $cuisine->id;
        if (!empty($slug)) {
            DB::table('cuisines')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);
            
            DB::table('menu_item')
                    ->where('cuisines_id', $cuisine_id)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Cuisine deactivated successfully');
        }
    }

    public function showAdmin_deletecuisine($slug = null) {
        
        if (!empty($slug)) {
             $cuisine = DB::table('cuisines')
                        ->where('slug', $slug)->first();
          $cuisine_id = $cuisine->id;
          
           DB::table('menu_item')
                    ->where('cuisines_id', $cuisine_id)
                    ->update(['status' => 0]);
           
            DB::table('cuisines')->where('slug', $slug)->delete();
            return Redirect::to('/admin/cuisine/admin_index')->with('success_message', 'Cuisine deleted successfully');
        } else {
            return Redirect::to('/admin/cuisine/admin_index');
        }
    }

}
