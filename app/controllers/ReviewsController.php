<?php

class ReviewsController extends BaseController {
    /*
      |--------------------------------------------------------------------------
      | Default User Controller
      |--------------------------------------------------------------------------
      |
      | You may wish to use controllers instead of, or in addition to, Closure
      | based routes. That's great! Here is an example controller method to
      | get you started. To route to this controller, just add the route:
      |
      |	Route::get('/', 'ReviewsController@showAdmin_index');
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
        $query = Review::sortable()
                ->join('users as u1', 'reviews.user_id', '=', DB::raw('u1.id'))
                ->join('users as u2', DB::raw('u2.id'), '=', 'reviews.caterer_id')
                ->where(function ($query) use ($search_keyword) {
            $query->where(DB::raw('u1.first_name'), 'LIKE', '%' . $search_keyword . '%')
            ->orwhere(DB::raw('u1.last_name'), 'LIKE', '%' . $search_keyword . '%')
            ->orwhere(DB::raw('u2.first_name'), 'LIKE', '%' . $search_keyword . '%')
            ->orwhere(DB::raw('u2.last_name'), 'LIKE', '%' . $search_keyword . '%')
            ->orwhere('reviews.comment', 'LIKE', '%' . $search_keyword . '%');
        });

        if (!empty($input['action'])) {
            $action = $input['action'];
            $idList = $input['chkRecordId'];
            switch ($action) {
                case "Activate":
                    DB::table('reviews')
                            ->whereIn('reviews.id', $idList)
                            ->update(array('status' => '1'));
                    Session::put('success_message', 'Review(s) activated successfully');
                    break;
                case "Deactivate":
                    DB::table('reviews')
                            ->whereIn('reviews.id', $idList)
                            ->update(array('status' => '0'));
                    Session::put('success_message', 'Review(s) deactivated successfully');
                    break;
                case "Delete":
                    DB::table('reviews')
                            ->whereIn('reviews.id', $idList)
                            ->delete();
                    Session::put('success_message', 'Review(s) deleted successfully');
                    break;
            }
        }

        $separator = implode("/", $separator);

        // Get all the users
        $query->select(DB::raw('u1.first_name as user_f_name'), DB::raw('u1.last_name as user_l_name'), DB::raw('u2.first_name as caterer_f_name'), DB::raw('u2.last_name as caterer_l_name'), 'reviews.created', 'reviews.comment', 'reviews.item', 'reviews.quality', 'reviews.packaging', 'reviews.delivery', 'reviews.slug', 'reviews.status', 'reviews.id');
        $users = $query->orderBy('reviews.id', 'desc')->sortable()->paginate(10);

        // Show the page
        return View::make('Reviews/adminindex', compact('users'))->with('search_keyword', $search_keyword)
                        ->with('searchByDateFrom', $searchByDateFrom)
                        ->with('searchByDateTo', $searchByDateTo);
    }

    public function showAdmin_active($slug = null) {
        if (!empty($slug)) {

            DB::table('reviews')
                    ->where('slug', $slug)
                    ->update(['status' => 1]);

            return Redirect::to('/admin/reviews/admin_index')->with('success_message', 'Review(s) activated successfully');
        }
    }

    public function showAdmin_deactive($slug = null) {
        if (!empty($slug)) {
            DB::table('reviews')
                    ->where('slug', $slug)
                    ->update(['status' => 0]);

            return Redirect::to('/admin/reviews/admin_index')->with('success_message', 'Review(s) deactivated successfully');
        }
    }

    public function showAdmin_delete($slug = null) {
        if (!empty($slug)) {
            DB::table('reviews')->where('slug', $slug)->delete();
            return Redirect::to('/admin/reviews/admin_index')->with('success_message', 'Review deleted successfully');
        }
    }

}
