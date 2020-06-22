<?php

class SponsorshipController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Sponsorship Controller
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
//    public function __construct(Sponsorship $cuisine) {
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
        // $cuisines = Sponsorship::where('first_name', '');
     //   $query = DB::table('cuisines');
         $query = Sponsorship::sortable();
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
                    return Redirect::back()->with('success_message', 'Sponsorship(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('cuisines')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    return Redirect::back()->with('success_message', 'Sponsorship(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('cuisines')
                            ->whereIn('id', $idList)
                            ->delete();
                    return Redirect::back()->with('success_message', 'Sponsorship(s) deleted successfully');
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
        $cuisines = $query->orderBy('id', 'asc')->sortable()->paginate(10);
        // Show the page

        return View::make('Sponsorships/adminindex', compact('cuisines'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

 

    public function showAdmin_edit($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();

        $cuisine = DB::table('sponsorship')
                        ->where('slug', $slug)->first();

        if (empty($cuisine))
            return Redirect::to('/admin/sponsorship/admin_index');
        
        $cuisine_id = $cuisine->id;


        if (!empty($input)) {
            $rules = array(
                'name' => 'required|unique:sponsorship,name,' . $cuisine_id, // make sure the first name field is not empty
                'description' => 'required', // make sure the first name field is not empty
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/sponsorship/Admin_edit/' . $cuisine->slug)
                                ->withErrors($validator)->withInput(Input::all());
            } else {

                $data = array(
                    'name' => $input['name'],
                    'price' => $input['price'],
                    'no_of_days' => $input['no_of_days'],
                    'description' => $input['description'],
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('sponsorship')
                        ->where('id', $cuisine_id)
                        ->update($data);


                return Redirect::to('/admin/sponsorship/admin_index')->with('success_message', 'Package updated successfully.');
            }
        } else {



            return View::make('/Sponsorships/admin_edit')->with('detail', $cuisine);
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

            return Redirect::back()->with('success_message', 'Sponsorship activated successfully');
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

            return Redirect::back()->with('success_message', 'Sponsorship deactivated successfully');
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
            return Redirect::to('/admin/cuisine/admin_index')->with('success_message', 'Sponsorship deleted successfully');
        } else {
            return Redirect::to('/admin/cuisine/admin_index');
        }
    }

}
