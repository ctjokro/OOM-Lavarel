<?php

class PageController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default Page Controller
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

    //protected $page;
    public function showAdmin_index() {
        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }

        $input = Input::all();
        $search_keyword = "";
        $searchByDateFrom = "";
        $searchByDateTo = "";
        $separator = array();
        // $pages = Page::where('first_name', '');
        $query = DB::table('pages');
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
                    DB::table('pages')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 1));
                    return Redirect::back()->with('success_message', 'Page(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('pages')
                            ->whereIn('id', $idList)
                            ->update(array('status' => 0));
                    return Redirect::back()->with('success_message', 'Page(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('pages')
                            ->whereIn('id', $idList)
                            ->delete();
                    return Redirect::back()->with('success_message', 'Page(s) deleted successfully');
                    break;
            }
        }

        if (isset($search_keyword) && $search_keyword != '') {
            $search_keyword = strip_tags($search_keyword);
            $separator[] = 'search_keyword:' . urlencode($search_keyword);
            $pageName = str_replace('_', '\_', $search_keyword);
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
        // Get all the pages
        $pages = $query->orderBy('id', 'desc')->paginate(30);
        // Show the page

        return View::make('Pages/adminindex', compact('pages'))->with('search_keyword', $search_keyword)
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
                'name' => 'required|unique:pages', // make sure the first name field is not empty
                'category' => 'required', // make sure the category field is not empty
                'description' => 'required', // make sure the description field is not empty
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/page/admin_add')->withErrors($validator)->withInput(Input::all());
            } else {


                $slug = $this->createUniqueSlug($name, 'pages');
                $savePage = array(
                    'name' => $name,
                    'category' => $input['category'],
                    'description' => $input['description'],
                    'status' => '1',
                    'slug' => $slug,
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('pages')->insert(
                        $savePage
                );

                return Redirect::to('/admin/page/admin_index')->with('success_message', 'Page saved successfully.');
            }
        } else {
            return View::make('/Pages/admin_add');
        }
    }

    public function showAdmin_editpage($slug = null) {

        if (!Session::has('adminid')) {
            return Redirect::to('/admin/login');
        }
        $input = Input::all();

        $page = DB::table('pages')
                        ->where('slug', $slug)->first();
        $page_id = $page->id;


        if (!empty($input)) {
            $rules = array(
                'name' => 'required|unique:pages,name,' . $page_id, // make sure the first name field is not empty
                'description' => 'required', // make sure the first name field is not empty
            );


            // run the validation rules on the inputs from the form
            $validator = Validator::make(Input::all(), $rules);

            // if the validator fails, redirect back to the form
            if ($validator->fails()) {

                return Redirect::to('/admin/page/Admin_editpage/' . $page->slug)
                                ->withErrors($validator)->withInput(Input::all());
            } else {


                $data = array(
                    'name' => $input['name'],
                    'category' => $input['category'],
                    'description' => $input['description'],
                    'status' => '1',
                    'created' => date('Y-m-d H:i:s'),
                );
                DB::table('pages')
                        ->where('id', $page_id)
                        ->update($data);


                return Redirect::to('/admin/page/admin_index')->with('success_message', 'Page updated successfully.');
            }
        } else {



            return View::make('/Pages/admin_editpage')->with('detail', $page);
        }
    }

    public function showAdmin_activepage($slug = null) {
        if (!empty($slug)) {
            DB::table('pages')
                    ->where('slug', $slug)
                    ->update(['status' => 1]);

            return Redirect::back()->with('success_message', 'Page activated successfully');
        }
    }

    public function showAdmin_deactivepage($slug = null) {
        if (!empty($slug)) {
            DB::table('pages')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::back()->with('success_message', 'Page deactivated successfully');
        }
    }

    public function showAdmin_deletepage($slug = null) {
        if (!empty($slug)) {
            DB::table('pages')->where('slug', $slug)->delete();
            return Redirect::to('/admin/page/admin_index')->with('success_message', 'Page deleted successfully');
        }
    }

    public function showIndex($slug = null) {

        if (!empty($slug)) {
            $pageDetail = DB::table('pages')
                    ->where('slug', $slug)
                    ->first();
            //$this->layout->content = View::make('page.index')->with('pageDetail',$pageDetail);


            if (empty($pageDetail))
                return Redirect::to('/');
            $this->layout->title = TITLE_FOR_PAGES . $pageDetail->name;
            $this->layout->content = View::make('/Pages/index')->with('pageDetail', $pageDetail);
        }
    }
    
     public function showIndexNew($slug = null) {
         
        $this->layout = View::make('layouts.defaultpage');   
        
        if (!empty($slug)) {
            $pageDetail = DB::table('pages')
                    ->where('slug', $slug)
                    ->first();
            //$this->layout->content = View::make('page.index')->with('pageDetail',$pageDetail);


            if (empty($pageDetail))
                return Redirect::to('/');
            $this->layout->title = TITLE_FOR_PAGES . $pageDetail->name;
            $this->layout->content = View::make('/Pages/index')->with('pageDetail', $pageDetail);
        }
    }

    public function showhowtoorder() {

        $this->layout->title = TITLE_FOR_PAGES . " How Do I Order";
        $this->layout->content = View::make('/Pages/howtoorder');
    }

    public function showData($slug = null) {

        if (!empty($slug)) {
            $pageDetail = DB::table('pages')
                    ->where('slug', $slug)
                    ->first();

            if (empty($pageDetail))
                return Redirect::to('/');

            //$this->layout->content = View::make('page.index')->with('pageDetail',$pageDetail);
            return View::make('/Pages/data')->with('pageDetail', $pageDetail);
        }
    }

}
