<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerCampaign;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PartnerCampaignController extends Controller
{
    public function index(Request $request)
    {	
        if ($request->ajax()) {
           // $query = PartnerCampaign::with('category')->query();
            $query = PartnerCampaign::with('category');
			
            return DataTables::of($query)
                ->addColumn('status_badge', function ($campaign) {
                    $color = $campaign->status === 'active' ? 'success' : ($campaign->status === 'closed' ? 'danger' : 'secondary');
                    return '<span class="badge badge-' . $color . '">' . ucfirst($campaign->status) . '</span>';
                })
				->addColumn('category', function ($campaign) {
					return $campaign->category
						? $campaign->category->name
						: '-';
				})
                ->addColumn('upload_deadline', fn($campaign) => date('Y-m-d', strtotime($campaign->upload_deadline)))
                ->addColumn('start_date', fn($campaign) => date('Y-m-d', strtotime($campaign->start_date)))
                ->addColumn('end_date', fn($campaign) => date('Y-m-d', strtotime($campaign->end_date)))
                ->addColumn('actions', function ($campaign) {
					$view = '<a href="'.route('partner-campaigns.show', $campaign->id).'" class="btn btn-sm btn-info mr-1">
								<i class="fas fa-eye"></i>
							</a>';

					$edit = '<a href="'.route('partner-campaigns.edit', $campaign->id).'" class="btn btn-sm btn-warning mr-1">
								<i class="fas fa-edit"></i>
							</a>';

					$delete = '
						<form action="'.route('partner-campaigns.destroy', $campaign->id).'" method="POST" style="display:inline;">
							'.csrf_field().method_field('DELETE').'
							<button class="btn btn-sm btn-danger delete-campaign"><i class="fas fa-trash"></i></button>
						</form>
					';

					return $view . $edit . $delete;
				})
                ->rawColumns(['status_badge','actions'])
                ->make(true);
        }

        return view('admin.partner_campaigns.index');
    }

    public function create()
    {
        $categories = Category::where('parent_id',null)->get();
        return view('admin.partner_campaigns.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:255',
            //'frequency'           => 'required|in:weekly,biweekly,monthly',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'upload_deadline'     => 'nullable|date|after_or_equal:start_date',
            'min_value'           => 'nullable|numeric',
            'min_quantity'        => 'required|integer',
            'goal_quantity'       => 'nullable|integer',
            'category_id'         => 'nullable|integer|exists:categories,id',
            'cart_timer_minutes'  => 'nullable|integer',
            'cart_max_volume'     => 'nullable|integer',
            'status'              => 'required|in:draft,active,closed',
        ]);

        PartnerCampaign::create($data);

        return redirect()->route('partner-campaigns.index')->with('success', 'Campaign created successfully!');
    }

    public function edit(PartnerCampaign $partnerCampaign)
    {
        $categories = Category::where('parent_id',null)->get();
        return view('admin.partner_campaigns.edit', compact('partnerCampaign','categories'));
    }

    public function update(Request $request, PartnerCampaign $partnerCampaign)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:255',
            //'frequency'           => 'required|in:weekly,biweekly,monthly',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'upload_deadline'     => 'nullable|date|after_or_equal:start_date',
            'min_value'           => 'nullable|numeric',
            'min_quantity'        => 'required|integer',
            'goal_quantity'       => 'nullable|integer',
            'category_id'         => 'nullable|integer|exists:categories,id',
            'cart_timer_minutes'  => 'nullable|integer',
            'cart_max_volume'     => 'nullable|integer',
            'status'              => 'required|in:draft,active,closed',
        ]);

        $partnerCampaign->update($data);

        return redirect()->route('partner-campaigns.index')->with('success', 'Campaign updated successfully!');
    }
	
	public function show(PartnerCampaign $partnerCampaign)
	{
		$campaign = $partnerCampaign->load('products.vendor', 'category');

		return view('admin.partner_campaigns.show', compact('campaign'));
	}
	
    public function destroy(PartnerCampaign $partnerCampaign)
    {
        $partnerCampaign->delete();
        return back()->with('success','Campaign deleted successfully!');
    }
}
