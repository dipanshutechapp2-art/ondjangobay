<?php
namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use App\Models\PartnerProduct;
use App\Models\PartnerCampaign;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PartnerProductImport;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class PartnerProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = PartnerProduct::where('vendor_id', auth()->id())
                ->with('campaign')
                ->select('partner_products.*');

            return DataTables::of($products)
                ->addColumn('campaign_name', fn($p) => $p->campaign->name ?? 'N/A')
                ->addColumn('discount', fn($p) =>
                    $p->old_price > 0
                        ? round((($p->old_price - $p->new_price) / $p->old_price) * 100, 1) . '%'
                        : '0%'
                )
                ->editColumn('status', function ($p) {
                    $color = $p->status === 'approved' ? 'success' :
                        ($p->status === 'rejected' ? 'danger' : 'warning');
                    return "<span class='badge bg-{$color}'>" . ucfirst($p->status) . "</span>";
                })
                ->addColumn('image', function ($p) {
                    $url = asset($p->image);
                    return "<img src='{$url}' width='60' height='60' class='rounded'>";
                })
                ->addColumn('created_at', function ($p) {
                    return $p->created_at->format('Y-m-d H:i:s');
                })
                ->rawColumns(['status', 'image'])
                ->make(true);
        }

        $campaigns = PartnerCampaign::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('vendor.partner_products.index', compact('campaigns'));
    }

    public function downloadTemplate(Request $request)
    {
        /* $campaignId = $request->get('campaign_id');
        if (!$campaignId) {
            return back()->with('error', 'Please select a campaign.');
        }
		*/  
        $file = public_path("templates/campaign/partner_upload_template.xlsx");

        if (!file_exists($file)) {
            return back()->with('error', 'Template not found for this campaign.');
        }

        return response()->download($file);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:partner_campaigns,id',
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        // clear previous import logs for this vendor+campaign (optional)
        // DB::table('partner_product_import_logs')->where('vendor_id', auth()->id())->where('partner_campaign_id', $request->campaign_id)->delete();

        Excel::import(new PartnerProductImport(auth()->id(), $request->campaign_id), $request->file('file'));

        // count failures for this vendor & campaign
        $failuresCount = DB::table('partner_product_import_logs')
            ->where('vendor_id', auth()->id())
            ->where('partner_campaign_id', $request->campaign_id)
            ->count();

        if ($failuresCount > 0) {
            return back()->with('success', "Upload processed. {$failuresCount} rows failed validation.")->with('show_import_errors', $request->campaign_id);
        }

        return back()->with('success', 'Products uploaded successfully for the selected campaign! Pending admin approval.');
    }

    // show import errors list
    public function importErrors(Request $request)
    {
        $logs = DB::table('partner_product_import_logs')
            ->where('vendor_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('vendor.partner_products.import_errors', compact('logs'));
    }

    // optional: clear logs for a campaign/vendor
    public function clearImportErrors(Request $request)
    {  
        $campaignId = $request->get('campaign_id');
        DB::table('partner_product_import_logs')
            ->where('vendor_id', auth()->id())
            ->when($campaignId, fn($q) => $q->where('partner_campaign_id', $campaignId))
            ->delete();

        return back()->with('success', 'Import errors cleared.');
    }
}
