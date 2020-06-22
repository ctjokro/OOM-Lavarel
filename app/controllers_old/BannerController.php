<?php

class BannerController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default User Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'HomeController@showWelcome');
      |
     */

    protected $layout = 'layouts.default';

    public function logincheck($url) {
        if (!Session::has('user_id')) {
            Session::put('return', $url);
            return Redirect::to('/')->with('error_message', 'You must login to see this page.');
        } else {

            $user_id = Session::get('user_id');
            $userData = DB::table('users')
                    ->where('id', $user_id)
                    ->first();
            if (empty($userData)) {
                Session::forget('user_id');
                return Redirect::to('/');
            }
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
        $query = Banner::sortable()
                //->where("status", "=", 'Banner')
                ->where(function ($query) use ($search_keyword) {
                    $query->where('title', 'LIKE', '%' . $search_keyword . '%');
                   
                });

        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('banner')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));
                    Session::put('success_message', 'Banner(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('banner')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                   Session::put('success_message', 'Banner(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('banner')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Banner(s) deleted successfully');
                    break;
            }
        }

        $separator = implode("/", $separator);

        // Get all the users
        $banners = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Banner/adminindex', compact('banners'))->with('search_keyword', $search_keyword)
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
                'title' => 'required', // make sure the first name field is not empty
                'file_name' => 'mimes:jpeg,png,jpg',
              
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/banner/admin_add')
                                ->withErrors($validator)
                                ->withInput(Input::all());
            } else {

                if (Input::hasFile('file_name')) {
                    $file = Input::file('file_name');
                    $profileImageName = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_BANNER_IMAGE_PATH, time() . $file->getClientOriginalName());
                    
                } else {
                    $profileImageName = "";
                }
                $slug = $this->createUniqueSlug($input['title'], 'users');
                $saveUser = array(
                    'title' => $input['title'],
                    'file_name' => $profileImageName,
                    'status' => '1',
                    'slug' => $slug,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('banner')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();
               
                return Redirect::to('/admin/banner/admin_index')->with('success_message', 'Banner saved successfully.');
            }
        } else {
            return View::make('/Banner/admin_add');
        }
    }

    public function showAdmin_edit($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();

        $user = DB::table('banner')
                        ->where('slug', $slug)->first();
        $user_id = $user->id;


        if (!empty($input)) {
            $old_file_name = $input['old_file_name'];
            $rules = array(
                'title' => 'required', // make sure the first name field is not empty
              
                'file_name' => 'mimes:jpeg,png,jpg',
                
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/banner/Admin_edit/' . $user->slug)
                                ->withErrors($validator) // send back all errors
                                ->withInput(Input::all());
            } else {



                if (Input::hasFile('file_name')) {
                    $file = Input::file('file_name');
                    $profileImageName = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_BANNER_IMAGE_PATH, time() . $file->getClientOriginalName());

                    @unlink(UPLOAD_BANNER_IMAGE_PATH . $old_file_name);
                } else {
                    $profileImageName = $old_file_name;
                }
                $data = array(
                    'title' => $input['title'],
                    'file_name' => $profileImageName,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('banner')
                        ->where('id', $user_id)
                        ->update($data);


                return Redirect::to('/admin/banner/admin_index')->with('success_message', 'Banner details updated successfully.');
            }
        } else {



            return View::make('/Banner/admin_edit')->with('detail', $user);
        }
    }

    public function showAdmin_active($slug = null) {
        if (!empty($slug)) {
            DB::table('banner')
                    ->where('slug', $slug)
                    ->update(['status' => 1]);

            return Redirect::back()->with('success_message', 'Banner(s) activated successfully');
        }
    }

    public function showAdmin_deactive($slug = null) {
        if (!empty($slug)) {
            DB::table('banner')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Banner(s) deactivated successfully');
        }
    }

    public function showAdmin_delete($slug = null) {
        if (!empty($slug)) {
            DB::table('banner')->where('slug', $slug)->delete();
            return Redirect::to('/admin/banner/admin_index')->with('success_message', 'Banner deleted successfully');
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
