<?php
namespace App\Services\DHL;

use App\Models\Order;
use Carbon\Carbon;

class DHLShipmentService extends DHLBaseService
{
    public function create($order_id)
    {  
		$order = Order::with('orderProduct.product.userInfo', 'orderTotal','vendor')->where('id', $order_id)->first();
		
		$items = $order->orderProduct;
		
		/* dd([
                "plannedShippingDateAndTime" => now()->addDay()->toIso8601String(),
                "productCode" => "P",

                "customerDetails" => [
                    "shipperDetails" => [
                        "postalAddress" => [
                            //"cityName" => $order->vendor->city,
                            "cityName" => 'Lucknow',
                            "countryCode" => 'IN',
                            "postalCode" => '226007',
                            "addressLine1" => '547 reserve police lines',
                        ],
                        "contactInformation" => [
                            //"fullName" => $order->vendor->name,
                            "fullName" => 'Sandeep Tripathi',
                            "phone" => '9936882864',
                            "email" => 'sandeepsvi1990@gmail.com',
                        ]
                    ],
                    "receiverDetails" => [
                        "postalAddress" => [
                            //"cityName" => $order->shipping_city,
                            "cityName" => 'Lucknow',
                            //"countryCode" => $order->shipping_country,
                            "countryCode" => '91',
                            "postalCode" => $order->shipping_zipcode,
                            "addressLine1" => $order->shipping_address_2,
                        ],
                        "contactInformation" => [
                            "fullName" => $order->shipping_first_name.' '.$order->shipping_last_name,
                            "phone" => $order->phone,
                            "email" => $order->email,
                        ]
                    ]
                ],

                "packages" => [
                    [
                        "weight" => max(1, $items->sum('quantity')),
                    ]
                ]
            ]); */
		
        /* $response = $this->client()->post(
            $this->baseUrl().'/shipments',
            [
                "plannedShippingDateAndTime" => now()->addDay()->toIso8601String(),
                "productCode" => "P",
                "customerDetails" => [
                    "shipperDetails" => [
                        "postalAddress" => [
                            "cityName" => 'Lucknow',
                            "countryCode" => 'IN',
                            "postalCode" => '226007',
                            "addressLine1" => '547 reserve police lines',
                        ],
                        "contactInformation" => [
                            "fullName" => 'Sandeep Tripathi',
                            "phone" => '9936882864',
                            "email" => 'sandeepsvi1990@gmail.com',
                        ]
                    ],
                    "receiverDetails" => [
                        "postalAddress" => [
                            "cityName" => 'Lucknow',
                            "countryCode" => 'IN',
                            "postalCode" => $order->shipping_zipcode,
                            "addressLine1" => $order->shipping_address_2,
                        ],
                        "contactInformation" => [
                            "fullName" => $order->shipping_first_name.' '.$order->shipping_last_name,
                            "phone" => $order->phone,
                            "email" => $order->email,
                        ]
                    ]
                ],

                "packages" => [
                    [
                        "weight" => max(1, $items->sum('quantity')),
                    ]
                ]
            ]
        ); */
		
		$plannedDate = Carbon::now()->addDay()->format('Y-m-d\TH:i:s \G\M\TP');
		
		$response = $this->client()->post(
			$this->baseUrl().'/shipments',
			[
				'plannedShippingDateAndTime' => $plannedDate,
				"productCode" => "N",

				"pickup" => [
					"isRequested" => false
				],

				"accounts" => [
					[
						"typeCode" => "shipper",
						"number" => config('dhl.account_number')
					],
					
				],

				"customerDetails" => [
					"shipperDetails" => [
						"postalAddress" => [
							"cityName" => "Lucknow",
							"countryCode" => "IN",
							"postalCode" => "226007",
							"addressLine1" => "547 reserve police lines",
						],
						"contactInformation" => [
							"companyName" => "My Company Pvt Ltd",
							"fullName" => "Sandeep Tripathi",
							"phone" => "9936882864",
							"email" => "sandeepsvi1990@gmail.com",
						]
					],
					"receiverDetails" => [
						"postalAddress" => [
							"cityName" => "Lucknow",
							"countryCode" => "IN",
							"postalCode" => $order->shipping_zipcode,
							"addressLine1" => $order->shipping_address_2,
						],
						"contactInformation" => [
							"companyName" => "Customer",
							"fullName" => $order->shipping_first_name.' '.$order->shipping_last_name,
							"phone" => $order->phone,
							"email" => $order->email,
						]
					]
				],

				"content" => [
				    "unitOfMeasurement" => "metric",
					"packages" => [
						[
							"weight" => max(1, $items->sum('quantity')),
							"dimensions" => [
								"length" => 10,
								"width"  => 10,
								"height" => 10
							]
						]
					],
					"isCustomsDeclarable" => false,
					"description" => "EXCS Ecommerce Order"
				]

			]
		);

		
		dd([
			'status'  => $response->status(),
			'headers' => $response->headers(),
			'json'    => $response->json(),
			'body'    => $response->body(),
		]);
        return $response->json();
    }
}
