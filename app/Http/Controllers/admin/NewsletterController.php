<?php

namespace App\Http\Controllers\admin;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\File;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        
		if ($request->ajax()) {
			
			$newsletters = NewsletterSubscription::orderBy('id','DESC')->get();
			
			return DataTables::of($newsletters)
				->addColumn('created_at', function($newsletters) {
					return date('Y-m-d H:i',strtotime($newsletters->created_at));
				})

				->addColumn('action', function($newsletters) {
					
					$deleteUrl = route('newsletters.destroy', $newsletters->id);
					$token = csrf_token();

					return '
						<div class="btn-group">
							<button type="button" class="btn btn-success">Action</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item delete-category" href="#" data-id="'.$newsletters->id.'">Delete</a>
								<form id="delete-form-'.$newsletters->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
									<input type="hidden" name="_token" value="'.$token.'">
									<input type="hidden" name="_method" value="DELETE">
								</form>
							</div>
						</div>
					';
				})
				->rawColumns(['action','status'])
				->make(true);
		}
		
        return view('admin.newsletters.list');
    }

    public function destroy(NewsletterSubscription $newsletter)
	{
		$newsletter->delete();

		return redirect()->route('newsletters.index')
			->with('success', 'Newsletter deleted successfully.');
	}

}
