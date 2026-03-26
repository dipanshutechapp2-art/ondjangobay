<?php
namespace App\Imports;

use App\Models\PartnerProduct;
use App\Models\PartnerCampaign;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class PartnerProductImport implements ToCollection
{
    protected $vendor_id;
    protected $campaign_id;
	protected $category_id;

    public function __construct($vendor_id, $campaign_id)
    {
        $this->vendor_id     = $vendor_id;
        $this->campaign_id   = $campaign_id;
		$campaign            = \App\Models\PartnerCampaign::find($campaign_id);
		$this->category_id   = $campaign->category_id;
    }

    private function logFailure($productName, $reason, $meta = null)
    {
        DB::table('partner_product_import_logs')->insert([
            'vendor_id' => $this->vendor_id,
            'partner_campaign_id' => $this->campaign_id,
            'product_name' => $productName,
            'reason' => $reason,
            'meta' => $meta ? json_encode($meta) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function collection(Collection $rows)
    {

        $campaign = PartnerCampaign::find($this->campaign_id);

        if (!$campaign) {
            $this->logFailure(null, 'Campaign not found.');
            return;
        }

        if ($campaign->status !== 'active') {
            $this->logFailure(null, 'Campaign is not active (status != active).', ['campaign_status' => $campaign->status]);
            return;
        }

        if ($campaign->upload_deadline && now()->startOfDay()->gt($campaign->upload_deadline)) {
            $this->logFailure(null, 'Upload deadline has passed for this campaign.', ['upload_deadline' => $campaign->upload_deadline]);
            return;
        }

        if ($campaign->end_date && now()->gt($campaign->end_date)) {
            $this->logFailure(null, 'Campaign end date has passed.', ['end_date' => $campaign->end_date]);
            return;
        }

        $rows->shift();

        foreach ($rows as $index => $row) {
            $name        = trim($row[0] ?? '');
            $description = trim($row[1] ?? '');
            $oldPriceRaw = $row[2] ?? null;
            $newPriceRaw = $row[3] ?? null;
            $minQuantityRaw = $row[4] ?? null;
            $maxQuantityRaw = $row[5] ?? null;
            $imageUrl = trim($row[6] ?? '');

            $rowMeta = [
                'row' => $index + 2, 
                'raw' => $row->toArray()
            ];

            if ($name === '' || $oldPriceRaw === null || $newPriceRaw === null) {
                $this->logFailure($name ?: 'Unnamed', 'Required fields missing (name / old_price / new_price).', $rowMeta);
                continue;
            }

            $oldPrice = floatval($oldPriceRaw);
            $newPrice = floatval($newPriceRaw);
            $minQuantity = intval($minQuantityRaw ?: 1);
            $maxQuantity = $maxQuantityRaw !== null ? intval($maxQuantityRaw) : null;

            if ($oldPrice <= 0 || $newPrice <= 0) {
                $this->logFailure($name, 'Price must be greater than 0.', $rowMeta);
                continue;
            }

            if ($oldPrice < $newPrice) {
                $this->logFailure($name, 'Old price cannot be less than new price.', $rowMeta);
                continue;
            }

            if ($campaign->min_value && $newPrice < floatval($campaign->min_value)) {
                $this->logFailure($name, "New price ({$newPrice}) is lower than campaign min_value ({$campaign->min_value}).", $rowMeta);
                continue;
            }

            if ($campaign->min_quantity && $minQuantity < intval($campaign->min_quantity)) {
                $this->logFailure($name, "Product min_quantity ({$minQuantity}) is less than campaign min_quantity ({$campaign->min_quantity}).", $rowMeta);
                continue;
            }

            if ($maxQuantity !== null && $maxQuantity < $minQuantity) {
                $this->logFailure($name, "Product max_quantity ({$maxQuantity}) cannot be less than min_quantity ({$minQuantity}).", $rowMeta);
                continue;
            }

            $existing = PartnerProduct::where('vendor_id', $this->vendor_id)
                ->where('partner_campaign_id', $this->campaign_id)
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->first();

            $imagePath = $existing->image ?? null;

            if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                try {
                    $response = Http::timeout(20)->get($imageUrl);

                    if ($response->successful()) {
                        $uploadDir = public_path('uploads/partner_products');
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0775, true);
                        }

                        if ($existing && !empty($existing->image)) {
                            $oldPath = public_path($existing->image);
                            if (file_exists($oldPath)) {
                                @unlink($oldPath);
                            }
                        }

                        $ext = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                        $filename = Str::random(12) . '.' . $ext;
                        $savePath = $uploadDir . '/' . $filename;
                        File::put($savePath, $response->body());
                        $imagePath = 'uploads/partner_products/' . $filename;
                    } else {
                        $this->logFailure($name, "Image URL responded with HTTP code {$response->status()}. Image skipped.", array_merge($rowMeta, ['image_url' => $imageUrl]));
                    }
                } catch (\Exception $e) {
                    $this->logFailure($name, "Image download failed: " . $e->getMessage(), array_merge($rowMeta, ['image_url' => $imageUrl]));
                }
            }
		
		
            if ($existing) {
                try {
                    $existing->update([
                        'category_id'   => $this->category_id,
						'description'   => $description,
                        'old_price'     => $oldPrice,
                        'new_price'     => $newPrice,
                        'min_quantity'  => $minQuantity,
                        'max_quantity'  => $maxQuantity,
                        'image'         => $imagePath,
                        'status'        => 'pending',
                    ]);
                } catch (\Exception $e) {
                    $this->logFailure($name, "DB update failed: " . $e->getMessage(), $rowMeta);
                }
            } else {
                try {
                    PartnerProduct::create([
                        'vendor_id'           => $this->vendor_id,
                        'partner_campaign_id' => $this->campaign_id,
						'category_id'         => $this->category_id,
                        'name'                => $name,
                        'description'         => $description,
                        'old_price'           => $oldPrice,
                        'new_price'           => $newPrice,
                        'min_quantity'        => $minQuantity,
                        'max_quantity'        => $maxQuantity,
                        'image'               => $imagePath,
                        'status'              => 'pending',
                    ]);
                } catch (\Exception $e) {
                    $this->logFailure($name, "DB insert failed: " . $e->getMessage(), $rowMeta);
                }
            }
        }
    }
}
