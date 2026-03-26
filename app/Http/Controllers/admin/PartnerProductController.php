<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerProduct;
use App\Models\PartnerCampaign;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Yajra\DataTables\Facades\DataTables;

class PartnerProductController extends Controller
{

	public function approve($id)
    {
        $product = PartnerProduct::findOrFail($id);
        $product->update(['status' => 'approved']);
        //NotificationService::notifyVendorApproval($product);
        return back()->with('success', 'Product approved!');
    }

    public function reject($id)
    {
        $product = PartnerProduct::findOrFail($id);
        $product->update(['status' => 'rejected']);
        //NotificationService::notifyVendorRejection($product);
        return back()->with('info', 'Product rejected.');
    }

	public function index(Request $request)
	{
		if ($request->ajax()) {
			$query = PartnerProduct::with(['vendor', 'campaign'])
				->when($request->campaign_id, fn($q) => $q->where('partner_campaign_id', $request->campaign_id))
				->when($request->status, fn($q) => $q->where('status', $request->status));

			return DataTables::of($query)
				->addColumn('vendor', fn($p) => $p->vendor->name ?? 'N/A')
				->addColumn('campaign', fn($p) => $p->campaign->name ?? 'N/A')
				->addColumn('discount', fn($p) =>
					$p->old_price > 0 
						? round((($p->old_price - $p->new_price) / $p->old_price) * 100, 1) . '%'
						: '0%'
				)
				->addColumn('image', fn($p) => "<img src='".asset($p->image)."' width='60' height='60' class='rounded'>")
				->addColumn('status_badge', function ($p) {
					$color = match($p->status) {
						'approved' => 'success',
						'rejected' => 'danger',
						default => 'warning',
					};
					return '<span class="badge badge-' . $color . '">' . ucfirst($p->status) . '</span>';
				})
				->addColumn('actions', function ($p) {
					if($p->status == 'pending' && $p->campaign->status == 'active') {
						return '
							<form action="'.route('admin.partner-products.approve', $p->id).'" method="POST" style="display:inline;">
								'.csrf_field().'
								<button class="btn btn-sm btn-success" onclick="return confirm(\'Approve this product?\')">
									<i class="fas fa-check"></i>
								</button>
							</form>
							<form action="'.route('admin.partner-products.reject', $p->id).'" method="POST" style="display:inline;">
								'.csrf_field().'
								<button class="btn btn-sm btn-danger" onclick="return confirm(\'Reject this product?\')">
									<i class="fas fa-times"></i>
								</button>
							</form>
						';
					}
					return '<span class="text-muted">No action</span>';
				})
				->rawColumns(['status_badge', 'actions','image'])
				->make(true);
		}

		$campaigns = PartnerCampaign::pluck('name', 'id');
		return view('admin.partner_products.index', compact('campaigns'));
	}

}
