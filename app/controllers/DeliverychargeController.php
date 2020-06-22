<?php

class DeliverychargeController extends BaseController {
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
//        $query = DB::table('delivery_charges')
//                ->select("delivery_charges.*", "cities.id as city_id","areas.id as area_id","from_cities.id as from_city_id", "from_cities.name as from_city_name", "to_cities.id as to_city_id", "to_cities.name as to_city_name", "from_area.id as from_area_id", "from_area.name as from_area_name", "to_area.id as to_area_id", "to_area.name as to_area_name"
//                )
//                ->join('cities as from_cities', 'city_id', '=', 'delivery_charges.from_city_id')
//                ->join('cities as to_cities', 'city_id', '=', 'delivery_charges.to_city_id')
//                ->join('areas as from_area', 'area_id', '=', 'delivery_charges.from_area_id')
//                ->join('areas as to_area', 'area_id', '=', 'delivery_charges.to_area_id');

        //$query = DB::table('delivery_charges');
         $query = DeliveryCharge::sortable();
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
                    DB::table('delivery_charges')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));
                    return Redirect::back()->with('success_message', 'Delivery Charge(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('delivery_charges')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    return Redirect::back()->with('success_message', 'Delivery Charge(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('delivery_charges')
                            ->whereIn('id', $idList)
                            ->delete();
                    return Redirect::back()->with('success_message', 'Delivery Charge(s) deleted successfully');
                    break;
            }
        }

        if (isset($search_keyword) && $search_keyword != '') {
            $search_keyword = strip_tags($search_keyword);
            $cityData = DB::table('cities')
                    ->where('name', 'LIKE', '%' . $search_keyword . '%')
                    ->get();
            if (!empty($cityData)) {
                foreach ($cityData as $cityId) {
                    $city_id_arr[] = $cityId->id;
                }
                $query->whereIn('from_city_id', $city_id_arr);
            } else {
                $query->where('from_city_id', $search_keyword);
            }
            $separator[] = 'search_keyword:' . urlencode($search_keyword);
            $cityName = str_replace('_', '\_', $search_keyword);
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
        $delivery_charges = $query->orderBy('id', 'desc')->sortable()->paginate(10);
        // Show the page

        return View::make('Deliverycharge/adminindex', compact('delivery_charges'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_add() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $input = Input::all();
        if (!empty($input)) {
            $from_area_id = $input['from_area_id'];
            $to_area_id = $input['to_area_id'];
            $rules = array(
                'from_city_id' => 'required',
                'from_area_id' => 'required',
                'to_city_id' => 'required',
                'to_area_id' => 'required',
                'basic_charge' => 'required',
                'advance_charge' => 'required',
                 'delivery_charge_limit' => 'required',
            );
            $messages = array(
                'basic_charge' => 'The vespa charge is required field.',
                'advance_charge' => 'The car charge is required field.',
            );

            $isFromId = DB::table('delivery_charges')
                    ->where('from_area_id', $from_area_id)
                    ->where('to_area_id', $to_area_id)
                    ->first();

            if ($isFromId) {
                return Redirect::to('/admin/deliverycharge/admin_add')->with('error_message', 'From area and to area already exits.');
            }

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules, $messages);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/deliverycharge/admin_add')->withErrors($validator)->withInput(Input::all());
            } else {

                $saveDeliveryCharge = array(
                    'from_city_id' => $input['from_city_id'],
                    'from_area_id' => $input['from_area_id'],
                    'to_city_id' => $input['to_city_id'],
                    'to_area_id' => $input['to_area_id'],
                    'basic_charge' => $input['basic_charge'],
                    'advance_charge' => $input['advance_charge'],
                    'delivery_charge_limit' => $input['delivery_charge_limit'],
                    'status' => '1',
                    'slug' => $this->createSlug('cart'),
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('delivery_charges')->insert(
                        $saveDeliveryCharge
                );

                return Redirect::to('/admin/deliverycharge/admin_index')->with('success_message', 'Delivery charges saved successfully.');
            }
        } else {
            return View::make('/Deliverycharge/admin_add');
        }
    }

    public function showAdmin_edit($slug = null) {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();

        $delivery_charges = DB::table('delivery_charges')
                        ->where('slug', $slug)->first();
        $delivery_charges_id = $delivery_charges->id;


        if (!empty($input)) {
            $from_area_id = $input['from_area_id'];
            $to_area_id = $input['to_area_id'];
            $rules = array(
                'from_city_id' => 'required',
                'from_area_id' => 'required',
                'to_city_id' => 'required',
                'to_area_id' => 'required',
                'basic_charge' => 'required',
                'advance_charge' => 'required',
                 'delivery_charge_limit' => 'required',
            );

            $isFromId = DB::table('delivery_charges')
                    ->where('from_area_id', $from_area_id)
                    ->where('to_area_id', $to_area_id)
                    ->where('id', '!=', $delivery_charges_id)
                    ->first();

            if ($isFromId) {
                return Redirect::to('/admin/deliverycharge/admin_add')->with('error_message', 'From area and to area already exits.');
            }


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/deliverycharge/Admin_edit/' . $delivery_charges->slug)
                                ->withErrors($validator)->withInput(Input::all());
            } else {

                $saveDeliveryCharge = array(
                    'from_city_id' => $input['from_city_id'],
                    'from_area_id' => $input['from_area_id'],
                    'to_city_id' => $input['to_city_id'],
                    'to_area_id' => $input['to_area_id'],
                    'basic_charge' => $input['basic_charge'],
                    'advance_charge' => $input['advance_charge'],
                    'delivery_charge_limit' => $input['delivery_charge_limit'],
                    'status' => '1',
                    'slug' => $this->createSlug('cart'),
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('delivery_charges')
                        ->where('id', $delivery_charges_id)
                        ->update($saveDeliveryCharge);

                return Redirect::to('/admin/deliverycharge/admin_index')->with('success_message', 'Delivery Charge updated successfully.');
            }
        } else {
            return View::make('/Deliverycharge/admin_edit')->with('detail', $delivery_charges);
        }
    }

    public function showAdmin_active($slug = null) {
        if (!empty($slug)) {
            DB::table('delivery_charges')
                    ->where('slug', $slug)
                    ->update(['status' => 1]);

            return Redirect::back()->with('success_message', 'Delivery Charges activated successfully');
        }
    }

    public function showAdmin_deactive($slug = null) {
        if (!empty($slug)) {
            DB::table('delivery_charges')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Delivery Charges deactivated successfully');
        }
    }

    public function showAdmin_delete($slug = null) {
        if (!empty($slug)) {
            DB::table('delivery_charges')->where('slug', $slug)->delete();
            return Redirect::to('/admin/deliverycharge/admin_index')->with('success_message', 'Delivery Charges deleted successfully');
        }
    }

    function createSlug($string) {
        $string = substr(strtolower($string), 0, 35);
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "");
        $return = strtolower(preg_replace($old_pattern, $new_pattern, $string)) . rand(111111, 9999999) . time();
        return $return;
    }

}
