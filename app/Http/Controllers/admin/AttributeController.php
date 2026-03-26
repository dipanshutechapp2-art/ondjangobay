<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\User;
use Auth;

class AttributeController extends Controller
{
    public function index(Request $request) {

		if ($request->ajax()) {
			
			$attributes = Attribute::with('values','vendor')->get();
			
			return DataTables::of($attributes)
				->addColumn('created_at', function($attributes) {
					return date('Y-m-d H:i',strtotime($attributes->created_at));
				})
				->addColumn('values', function ($attribute) {
					return $attribute->values->pluck('value')->implode(', ');
				})
				->addColumn('vendor_name', function ($attribute) {
					return $attribute->vendor->name ?? "";
				})
				->addColumn('action', function($attributes) {
					
					$editUrl   = route('admin.attributes.edit', $attributes->id);
					$deleteUrl = route('admin.attributes.destroy', $attributes->id);
					$token = csrf_token();
			   
					return '
						<div class="btn-group">
							<button type="button" class="btn btn-success">Action</button>
							<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu" role="menu">
								<a class="dropdown-item" href="'.$editUrl.'" data-id="'.$attributes->id.'" data-name="'.$attributes->name.'">Edit</a>
								<a class="dropdown-item delete-category" href="#" data-id="'.$attributes->id.'">Delete</a>
								<form id="delete-form-'.$attributes->id.'" action="'.$deleteUrl.'" method="POST" style="display: none;">
									<input type="hidden" name="_token" value="'.$token.'">
									<input type="hidden" name="_method" value="DELETE">
								</form>
							</div>
						</div>
					';
				})
				->rawColumns(['product_name','varients_name','action','values','vendor_name'])
				->make(true);
		}
		return view('admin.attributes.list');
    }

    public function create() {
		
		$vendors = User::where('role','vendor')->get();
		
		return view('admin.attributes.create',compact('vendors'));
    }

    public function store(Request $request) {
		

		$data = $request->validate([
			//'name'       => 'required|string|unique:attributes,name',
			'name'       => ['required','string',
								Rule::unique('attributes')->where(function ($query) use ($request) {
									return $query->where('vendor_id', $request->vendor_id);
								}),
							],					
			'vendor_id'  => 'required|numeric|exists:users,id',	
			'values'     => 'required|array|min:1',
            'values.*'   => 'required|string'
		]);

		
		$attribute = new Attribute;
		$attribute->name       = $request->name;
		$attribute->vendor_id  = $request->vendor_id;
		$attribute->save();
		
		foreach ($request->values as $value) {
			$attribute->values()->create([
				'value'     => $value,
				'vendor_id' => $request->vendor_id,
			]);
		}
		
		return redirect()->back()->with('success', 'Created successfully.');
    }

    public function edit($id) {
		
		$vendors        = User::where('role','vendor')->get();
        $attributes     = Attribute::findOrFail($id);

        return view('admin.attributes.edit', compact('attributes','vendors'));
    }

	public function update(Request $request, string $id) {
	  
		try {
			
			$data = $request->validate([
				//'name'       => 'required|string|unique:attributes,name,'.$id,
				'name'       => ['required','string',
									Rule::unique('attributes')
									->where(fn ($query) => $query->where('vendor_id', $request->vendor_id))
									->ignore($id),
								],			
				'vendor_id'  => 'required|numeric|exists:users,id',	
                'values'     => 'required|array|min:1',
				'values.*'   => 'required|string',
				'value_ids'  => 'required|array',				
			]);
			
			
			$attribute = Attribute::findOrFail($id);
			$attribute->name        = $request->name;
			$attribute->vendor_id   = $request->vendor_id;
			$attribute->save();
			
			$existingIds = $attribute->values()->pluck('id')->toArray();
			$submittedIds = array_filter($request->value_ids); 

			$idsToDelete = array_diff($existingIds, $submittedIds);
			
			if (!empty($idsToDelete)) {
				AttributeValue::destroy($idsToDelete);
			}

			foreach ($request->values as $index => $val) {
				$valueId = $request->value_ids[$index];

				if ($valueId && $valueId != 0) {
					AttributeValue::where('id', $valueId)->update([
						'value'     => $val,
						'vendor_id' => $request->vendor_id,
					]);
				} else {
					$attribute->values()->create([
						'value'     => $val,
						'vendor_id' => $request->vendor_id,
					]);
				}
			}
			
			
			return redirect()->route('admin.attributes.index')->with('success', 'Updated successfully.');
			
        } catch (\Throwable $th) {
			
            return redirect()->back()->with('error', 'Failed to update: ' . $th->getMessage())->withInput();
        }
    }

    public function destroy($attributeId) {
		
		$attribute = Attribute::with('values')->findOrFail($attributeId);
		$attribute->values()->delete();
		$attribute->delete();
		
		return redirect()->back()->with('success', 'Attributes deleted successfully.');
	}
}
