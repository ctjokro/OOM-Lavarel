<?php

class CouponController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Coupon Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'CouponController@showWelcome');
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
        $query = Coupon::sortable()
                ->where(function ($query) use ($search_keyword) {
            $query->where('code', 'LIKE', '%' . $search_keyword . '%');
        });
        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('coupons')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));

                    Session::put('success_message', 'Coupon(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('coupons')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    Session::put('success_message', 'Coupon(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('coupons')
                            ->whereIn('id', $idList)
                            ->delete();
                    Session::put('success_message', 'Coupon(s) deleted successfully');
                    break;
            }
        }

        $separator = implode("/", $separator);

        // Get all the coupons
        $coupons = $query->orderBy('id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Coupons/adminindex', compact('coupons'))->with('search_keyword', $search_keyword)
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
                'code' => 'required|unique:coupons',
                'discount' => 'required',
                'coupon_image' => 'mimes:jpeg,png,jpg',
                'start_time' => 'required',
                'end_time' => 'required',
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/coupon/admin_add')->withErrors($validator)->withInput(Input::all());
            } else {

                if (Input::hasFile('coupon_image')) {
                    $file = Input::file('coupon_image');
                    $imageName = time() . $file->getClientOriginalName();
                    $file->move(UPLOAD_FULL_COUPON_IMAGE_PATH, time() . $file->getClientOriginalName());
                } else {
                    $imageName = "";
                }
                $slug = $this->createUniqueSlug($input['code'], 'coupons');
                $saveUser = array(
                    'status' => '1',
                    'code' => $input['code'],
                    'start_time' => $input['start_time'],
                    'discount' => $input['discount'],
                    'end_time' => $input['end_time'],
                    'coupon_image' => $imageName,
                    'slug' => $slug,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('coupons')->insert(
                        $saveUser
                );
                $id = DB::getPdo()->lastInsertId();

                return Redirect::to('/admin/coupon/admin_index')->with('success_message', 'Coupon saved successfully.');
            }
        } else {
            return View::make('/Coupons/admin_add');
        }
    }

    public function showAdmin_active($slug = null) {
        if (!empty($slug)) {
            DB::table('coupons')
                    ->where('slug', $slug)
                    ->update(['status' => 1, 'status' => 1]);

            return Redirect::back()->with('success_message', 'Coupon(s) activated successfully');
        }
    }

    public function showAdmin_deactive($slug = null) {
        if (!empty($slug)) {
            DB::table('coupons')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Coupon(s) deactivated successfully');
        }
    }

    public function showAdmin_delete($slug = null) {
        if (!empty($slug)) {
            DB::table('coupons')->where('slug', $slug)->delete();
            return Redirect::to('/admin/coupon/admin_index')->with('success_message', 'Coupon deleted successfully');
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
