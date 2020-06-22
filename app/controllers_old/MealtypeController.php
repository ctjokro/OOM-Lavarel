<?php

class MealtypeController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Mealtype Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'MealtypeController@showWelcome');
      |
     */

    protected $layout = 'layouts.default';

    public function logincheck($url) {

        if (!Session::has('user_id')) {
            Session::put('return', $url);
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        }
    }

    public function showAdmin_index() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();
        if (!empty($input['search'])) {
            $search_keyword = trim($input['search']);
        }
        $query = Mealtype::sortable()
                ->where(function ($query) use ($search_keyword) {
            $query->where('name', 'LIKE', '%' . $search_keyword . '%');
        });
        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('mealtypes')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));

                    Session::put('success_message', 'Mealtype(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('mealtypes')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Mealtype(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('mealtypes')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Mealtype(s) deleted successfully');
                    break;
            }
        }

        $separator = implode("/", $separator);

        // Get all the coupons
        $mealtype = $query->orderBy('name', 'asc')->sortable()->paginate(10);

        // Show the page
        return View::make('Mealtypes/adminindex', compact('mealtype'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_add() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $input = Input::all();
        if (!empty($input)) {

            $rules = array(
                'name' => 'required|unique:mealtypes'
            
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/mealtype/admin_add')->withErrors($validator)->withInput(Input::all());
            } else {

                
                $slug = $this->createUniqueSlug($input['name'], 'mealtypes');
                $saveUser = array(
                    'status' => '1',
                    'name' => $input['name'],
                    'slug' => $slug,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('mealtypes')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();

                return Redirect::to('/admin/mealtype/admin_index')->with('success_message', 'Mealtype saved successfully.');
            }
        } else {
            return View::make('/Mealtypes/admin_add');
        }
    }
    
    public function showAdmin_edit($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();

        $mealtypes = DB::table('mealtypes')
                        ->where('slug', $slug)->first();

        if (empty($mealtypes))
            return Redirect::to('/admin/mealtype/admin_index');
        
        $mealtypes_id = $mealtypes->id;


        if (!empty($input)) {
            $rules = array(
                'name' => 'required|unique:mealtypes,name,' . $mealtypes_id, // make sure the first name field is not empty
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/mealtype/Admin_edit/' . $mealtypes->slug)
                                ->withErrors($validator)->withInput(Input::all());
            } else {

                $data = array(
                    'name' => $input['name'],
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('mealtypes')
                        ->where('id', $mealtypes_id)
                        ->update($data);


                return Redirect::to('/admin/mealtype/admin_index')->with('success_message', 'Mealtype updated successfully.');
            }
        } else {



            return View::make('/Mealtypes/admin_edit')->with('detail', $mealtypes);
        }
    }

    public function showAdmin_active($slug = null) {
        if (!empty($slug)) {
            DB::table('mealtypes')
                    ->where('slug', $slug)
                    ->update(['status' => 1, 'status' => 1]);

            return Redirect::back()->with('success_message', 'Mealtype(s) activated successfully');
        }
    }

    public function showAdmin_deactive($slug = null) {
        if (!empty($slug)) {
            DB::table('mealtypes')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Mealtype(s) deactivated successfully');
        }
    }

    public function showAdmin_delete($slug = null) {
        if (!empty($slug)) {
            DB::table('mealtypes')->where('slug', $slug)->delete();
            return Redirect::to('/admin/mealtype/admin_index')->with('success_message', 'Mealtype deleted successfully');
        }
    }

// --------------------------------------------------------------------
// Create slug for secure URL
    function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }

}
