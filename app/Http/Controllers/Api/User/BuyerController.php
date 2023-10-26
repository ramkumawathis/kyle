<?php

namespace App\Http\Controllers\Api\User;

use Carbon\Carbon;
use App\Models\Buyer;
use App\Models\User;
use App\Models\UserBuyerLikes;
use App\Models\PurchasedBuyer;
use App\Models\Token;
use Illuminate\Support\Str;
use App\Models\SearchLog;
use App\Imports\BuyersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreSingleBuyerDetailsRequest;
use App\Http\Requests\UpdateSingleBuyerDetailsRequest;


class BuyerController extends Controller
{
    
    public function singleBuyerFormElementValues(){
        $elementValues = [];

        try{

            if (Cache::has('singleFormElementDetails')){
                $responseData = [
                    'status'        => true,
                    'result'        => Cache::get('singleFormElementDetails'),
                ];
                return response()->json($responseData, 200);
            }else{

                $elementValues['market_preferances'] = collect(config('constants.market_preferances'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => $label,
                    ];
                })->values()->all();               
                
                $elementValues['property_types'] = collect(config('constants.property_types'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => $label,
                    ];
                })->values()->all();

                $elementValues['park'] = collect(config('constants.park'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => $label,
                    ];
                })->values()->all();

                $elementValues['building_class_values'] = collect(config('constants.building_class_values'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => ucwords(strtolower($label)),
                    ];
                })->values()->all();

                $elementValues['purchase_methods'] = collect(config('constants.purchase_methods'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => ucwords(strtolower($label)),
                    ];
                })->values()->all();

                $elementValues['parking_values'] = collect(config('constants.parking_values'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => ucwords(strtolower($label)),
                    ];
                })->values()->all();

                $elementValues['location_flaws'] = collect(config('constants.property_flaws'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => ucwords(strtolower($label)),
                    ];
                })->values()->all();

                $elementValues['buyer_types'] = collect(config('constants.buyer_types'))->map(function ($label, $value) {
                    if(in_array($value,array(5,11))){
                        return [
                            'value' => $value,
                            'label' => ucwords(strtolower($label)),
                        ];
                    }
                })->whereNotNull('value')->values()->all();

                $elementValues['zonings'] = collect(config('constants.zonings'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => ucwords(strtolower($label)),
                    ];
                })->values()->all();

                $elementValues['utilities'] = collect(config('constants.utilities'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => ucwords(strtolower($label)),
                    ];
                })->values()->all();

                $elementValues['sewers'] = collect(config('constants.sewers'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => ucwords(strtolower($label)),
                    ];
                })->values()->all();

                $elementValues['contact_preferances'] = collect(config('constants.contact_preferances'))->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => ucwords(strtolower($label)),
                    ];
                })->values()->all();

                $states = DB::table('states')->where('country_id',233)->where('flag','=',1)->orderBy('name','ASC')->pluck('name','id');

                $elementValues['states'] = $states->map(function ($label, $value) {
                    return [
                        'value' => $value,
                        'label' => ucwords(strtolower($label)),
                    ];
                })->values()->all();

                Cache::put('singleFormElementDetails',$elementValues);

                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'result'        => $elementValues,
                ];
                return response()->json($responseData, 200);
            }

        } catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'result'        => $elementValues,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);

        }
    }

    public function uploadSingleBuyerDetails(StoreSingleBuyerDetailsRequest $request){
        DB::beginTransaction();
        try {
            
            $isMailSend = false;
            $validatedData = $request->all();

            // Start create users table
            $userDetails =  [
                'first_name'     => $validatedData['first_name'],
                'last_name'      => $validatedData['last_name'],
                'name'           => ucwords($validatedData['first_name'].' '.$validatedData['last_name']),
                'email'          => $validatedData['email'], 
                'phone'          => $validatedData['phone'], 
            ];
            $createUser = User::create($userDetails);
            // End create users table

            if($createUser){
                //Assign buyer role
                $createUser->roles()->sync(3);

                $validatedData['user_id'] = auth()->user()->id;

                $validatedData['buyer_user_id'] = $createUser->id;

                $validatedData['country'] =  DB::table('countries')->where('id',233)->value('name');
    
                // if($request->state){
                //      $validatedData['state'] = json_encode($request->state);
                // }
                
                //  if($request->city){
                //      $validatedData['city'] = json_encode($request->city);
                // }
                
                if($request->parking){
                    $validatedData['parking'] = (int)$request->parking;
                }
              
                if($request->buyer_type){
                    $validatedData['buyer_type'] = (int)$request->buyer_type;
                }
    
               
                if($request->zoning){
                    $validatedData['zoning'] = json_encode($request->zoning);
                }           
               
                if($request->permanent_affix){
                    $validatedData['permanent_affix'] = (int)$request->permanent_affix;
                } 
                if($request->park){
                    $validatedData['park'] = (int)$request->park;
                }  
                if($request->rooms){
                    $validatedData['rooms'] = (int)$request->rooms;
                }
                
                // $createdBuyer = Buyer::create($validatedData);

                $createUser->buyerVerification()->create(['user_id'=>$validatedData['user_id']]);

                $validatedData = collect($validatedData)->except(['first_name', 'last_name','email','phone'])->all();

                $createUser->buyerDetail()->create($validatedData);

                if($createUser->buyerDetail && auth()->user()->is_seller){
                    //Purchased buyer
                    $syncData['buyer_id'] = $createUser->buyerDetail->id;
                    $syncData['created_at'] = Carbon::now();
                
                    auth()->user()->purchasedBuyers()->create($syncData);

                    $isMailSend = true;
                }

                if($isMailSend){
                    //Verification mail sent
                    $createUser->NotificationSendToBuyerVerifyEmail();
                }
            }
            

            DB::commit();
            
            //Success Response Send
            $responseData = [
                'status'            => true,
                'message'           => trans('messages.auth.buyer.register_success_alert'),
            ];
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            //  dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response   
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }
    }

    public function edit(){
        try{
            $userId = auth()->user()->id;
            $buyer = User::with('buyerDetail')->where('id',$userId)->first();

            $buyer->profile_image_url = $buyer->profile_image_url;

            $buyer_details = [
                'first_name' => $buyer->first_name ?? null,
                'last_name'  => $buyer->last_name ?? null,
                'name'       => $buyer->name ?? null,
                'email'      => $buyer->email ?? null,
                'phone'      => $buyer->phone ?? null,
                'profile_image' => $buyer->profile_image_url ?? null,
                'is_active'  => $buyer->is_active ?? 0,
            ];

            $buyer_details = collect($buyer_details);
            $other_details = $buyer->buyerDetail()->select('occupation','replacing_occupation','company_name','address','country','state','city','zip_code','price_min','price_max','bedroom_min','bedroom_max','bath_min','bath_max','size_min','size_max','lot_size_min','lot_size_max','build_year_min','build_year_max','arv_min','arv_max','parking','property_type','property_flaw','solar','pool','septic','well','age_restriction','rental_restriction','hoa','tenant','post_possession','building_required','foundation_issues','mold','fire_damaged','rebuild','squatters','buyer_type','max_down_payment_percentage','max_down_payment_money','max_interest_rate','balloon_payment','unit_min','unit_max','building_class','value_add','purchase_method','stories_min','stories_max','zoning','utilities','sewer','market_preferance','contact_preferance','is_ban','permanent_affix','park','rooms')->first();

            //Start State Column
            $states = DB::table('states')->where('flag','=',1)->whereIn('id',$other_details->state)->orderBy('name','ASC')->pluck('name','id');

            $other_details->state = $states->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => ucwords(strtolower($label)),
                ];
            })->values()->all();
            //End State Column

            //Start City Column
            $cities = DB::table('cities')->whereIn('id',$other_details->city)->orderBy('name','ASC')->pluck('name','id');

            $other_details->city = $cities->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => ucwords(strtolower($label)),
                ];
            })->values()->all();
            //End City Column

            //Start Market Preference (MLS Status) Column
            $mls_status_arr = \Arr::only(config('constants.market_preferances'), $other_details->market_preferance);
            $other_details->market_preferance = collect($mls_status_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Market Preference (MLS Status) Column

            //Start Contact Preference Column
            $contact_pref_arr = \Arr::only(config('constants.contact_preferances'), $other_details->contact_preferance);
            $other_details->contact_preferance = collect($contact_pref_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Contact Preference Column

            //Start Property Type Column
            $property_type_arr = \Arr::only(config('constants.property_types'), $other_details->property_type);
            $other_details->property_type = collect($property_type_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Property Type Column

            //Start Purchase Method Column
            $purchase_method_arr = \Arr::only(config('constants.purchase_methods'), $other_details->purchase_method);
            $other_details->purchase_method = collect($purchase_method_arr)->map(function ($label, $value) {
                 return [
                     'value' => $value,
                     'label' => $label,
                 ];
            })->values()->all();  
            //End Purchase Method Column

            //Start Zoning Column
            $zoning_arr = \Arr::only(config('constants.zonings'), json_decode($other_details->zoning));
            $other_details->zoning = collect($zoning_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Zoing Column

            //Start Utilities Column
            $utilities_arr = \Arr::only(config('constants.utilities'), $other_details->utilities);
            $other_details->utilities = collect($utilities_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Utilities Column

            //Start Sewage Column
            $sewer_arr = \Arr::only(config('constants.sewers'), $other_details->sewer);
            $other_details->sewer = collect($sewer_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Sewage Column

            //Start Building Class Column
            $building_class_arr = \Arr::only(config('constants.building_class_values'), $other_details->building_class);
            $other_details->building_class = collect($building_class_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Building Class Column

            //Start Parking Column
            $parking_arr = \Arr::only(config('constants.parking_values'), $other_details->parking);
            $other_details->parking = collect($parking_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Parking Column

            //Start Buyer Type Column
            $buyer_type_arr = \Arr::only(config('constants.buyer_types'), $other_details->buyer_type);
            $other_details->buyer_type = collect($buyer_type_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Buyer Type Column

            //Start Park Column
            $park_arr = \Arr::only(config('constants.park'), $other_details->park);
            $other_details->park = collect($park_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Park Column

            //Start Location Flaw Column
            $property_flaw_arr = \Arr::only(config('constants.property_flaws'), $other_details->property_flaw);
            $other_details->property_flaw = collect($property_flaw_arr)->map(function ($label, $value) {
                return [
                    'value' => $value,
                    'label' => $label,
                ];
            })->values()->all();  
            //End Location Flaw Column

            $mergedBuyerDetails = $buyer_details->merge($other_details);

            //Return Success Response
            $responseData = [
                'status'       => true,
                'buyer'        => $mergedBuyerDetails,
            ];
            return response()->json($responseData, 200);

        }catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);

        }
    }

    public function updateSingleBuyerDetails(UpdateSingleBuyerDetailsRequest $request){
    
        DB::beginTransaction();
        try {
            
            $authUserId = auth()->user()->id;

            $validatedData = $request->all();

            // Start create users table
            $userDetails =  [
                'first_name'     => $validatedData['first_name'],
                'last_name'      => $validatedData['last_name'],
                'name'           => ucwords($validatedData['first_name'].' '.$validatedData['last_name']),
            ];
        
            $updateUser = User::where('id',$authUserId)->update($userDetails);
            // End create users table

            if($updateUser){
                
                $validatedData['country'] =  DB::table('countries')->where('id',233)->value('name');
    
                // if($request->state){
                //      $validatedData['state'] = json_encode($request->state);
                // }
                
                //  if($request->city){
                //      $validatedData['city'] = json_encode($request->city);
                // }
                
                if($request->parking){
                    $validatedData['parking'] = (int)$request->parking;
                }
              
                if($request->buyer_type){
                    $validatedData['buyer_type'] = (int)$request->buyer_type;
                }
    
               
                if($request->zoning){
                    $validatedData['zoning'] = json_encode($request->zoning);
                }           
               
                if($request->permanent_affix){
                    $validatedData['permanent_affix'] = (int)$request->permanent_affix;
                } 
                if($request->park){
                    $validatedData['park'] = (int)$request->park;
                }  
                if($request->rooms){
                    $validatedData['rooms'] = (int)$request->rooms;
                }
                
                // $createUser->buyerVerification()->create(['user_id'=>$validatedData['user_id']]);

                $validatedData = collect($validatedData)->except(['first_name', 'last_name','email','phone'])->all();

                auth()->user()->buyerDetail()->update($validatedData);

            }
            

            DB::commit();
            
            //Success Response Send
            $responseData = [
                'status'            => true,
                'message'           => trans('messages.edit_success_message'),
            ];
            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            DB::rollBack();
             dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response   
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }
    }

    public function fetchBuyers(Request $request){
        $validator = Validator::make($request->all(), [
            'property_type'  => 'int',
        ]);

        if($validator->fails()){
             //Error Response Send
             $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 400);
        }

        DB::beginTransaction();
        try {
            $perPage = 10;
            $userId = auth()->user()->id;
            $totalBuyers = Buyer::whereRelation('buyersPurchasedByUser', 'user_id', '=', $userId)->count();
            $buyers = Buyer::select('id','first_name','last_name','email','phone')->whereRelation('buyersPurchasedByUser', 'user_id', '=', $userId)->orderBy('created_by','desc')->paginate($perPage);
        
            foreach ($buyers as $buyer) {
                $buyer->contact_preferance = $buyer->contact_preferance ? config('constants.contact_preferances')[$buyer->contact_preferance]: '';
                $buyer->redFlag = $buyer->redFlagedData()->where('user_id',$userId)->exists();
                $buyer->totalBuyerLikes = totalLikes($buyer->id);
                $buyer->totalBuyerUnlikes = totalUnlikes($buyer->id);
                $buyer->redFlagShow = $buyer->buyersPurchasedByUser()->where('user_id',auth()->user()->id)->exists();
                $buyer->createdByAdmin = ($buyer->created_by == 1) ? true : false;
            }

            DB::commit();

            //Return Success Response
            $responseData = [
                'status'        => true,
                'buyers'        => $buyers,
                'totalBuyers'   => $totalBuyers
            ];

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }

    }

    public function import(Request $request)
    {
        $request->validate([
            'csvFile' => 'required|mimes:csv,xlsx,xls',
        ]);

        // Create an array of rules for each column in the CSV sheet.
        $import = new BuyersImport;
        Excel::import($import, $request->file('csvFile'));

        try {
            $totalCount         = $import->totalRowCount();
            $insertedRowCount   = $import->insertedCount();
            $skippedCount       = $totalCount - $insertedRowCount;

            // dd($totalCount, $insertedRowCount, $skippedCount);
            
            if($insertedRowCount == 0){
                //Return Error Response
                $responseData = [
                    'status'        => false,
                    'message'       => trans('No rows inserted during the import process.'),
                ];
                return response()->json($responseData, 400);

            } else if($skippedCount > 0 && $insertedRowCount > 0){
                $message = "{$insertedRowCount} out of {$totalCount} rows inserted successfully.";
            
                //Return Error Response
                $responseData = [
                    'status'        => true,
                    'message'       => $message,
                ];
                return response()->json($responseData, 200);

            } else if($skippedCount == 0) {
                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'message'       => 'Buyers imported successfully!',
                ];
                return response()->json($responseData, 200);
            }

        }catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }
    }

    public function redFlagBuyer(Request $request){
        DB::beginTransaction();
        try {
           
            $validator = Validator::make([
                'reason' => $request->reason,
                              
            ], [
                'reason' => ['required'],                
            ]);            
                       
            // Check if validation fails
            if ($validator->fails()) {
                //Error Response Send
            $responseData = [
                'status'        => false,
                'validation_errors' => $validator->errors(),
            ];
            return response()->json($responseData, 400);
            }           
            
            $authUserId = auth()->user()->id;
            $redFlagRecord[$authUserId]['reason'] = $request->reason;
            $redFlagRecord[$authUserId]['incorrect_info'] = $request->incorrect_info;
            $buyer = Buyer::find($request->buyer_id);
           
            if(!$buyer->redFlagedData()->exists()){
                // $buyer->redFlagedData()->sync($redFlagRecord);
                $buyer->redFlagedData()->attach($redFlagRecord);

                $buyer->updated_at = \Carbon\Carbon::now();

                $buyer->save();
                DB::commit();
                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'message'       => 'Flag added successfully!',
                ];
                
                return response()->json($responseData, 200);
            }else{
                 //Return Error Response
                $responseData = [
                    'status'        => false,
                    'error'         => 'Flag already added!',
                ];
                return response()->json($responseData, 400);
            }
        }catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }
    }

    public function unhideBuyer(Request $request){
        DB::beginTransaction();
        try {
            $authUser = auth()->user();
            if($authUser->credit_limit > 0 ){
                $fetchBuyer = Buyer::query()->select('id','user_id','first_name','last_name','email','phone','contact_preferance','created_by')->where('id',$request->buyer_id)->first();
                if(auth()->user()->is_seller){    
                    $authUser->credit_limit = !empty($authUser->credit_limit) ? (int)$authUser->credit_limit-1 : 0;
                    $authUser->save();
                    //Purchased buyer
                    $syncData['buyer_id'] = $fetchBuyer->id;
                    $syncData['created_at'] = Carbon::now();
                    auth()->user()->purchasedBuyers()->create($syncData);
                }
                
                $fetchBuyer->name = $fetchBuyer->first_name." ".$fetchBuyer->last_name;
                $fetchBuyer->redFlag = $fetchBuyer->redFlagedData()->where('user_id',auth()->user()->id)->exists();
                $fetchBuyer->totalBuyerLikes = totalLikes($fetchBuyer->id);
                $fetchBuyer->totalBuyerUnlikes = totalUnlikes($fetchBuyer->id);
                $fetchBuyer->redFlagShow = $fetchBuyer->buyersPurchasedByUser()->exists();
                $fetchBuyer->createdByAdmin =  ($fetchBuyer->created_by == 1) ? true : false;
                $fetchBuyer->contact_preferance = $fetchBuyer->contact_preferance ? config('constants.contact_preferances')[$fetchBuyer->contact_preferance]: '';
                $fetchBuyer->liked=false;
                $fetchBuyer->disliked=false;
               
                DB::commit();

                
    
                //Success Response Send
                $responseData = [
                    'status'   => true,
                    'buyer' => $fetchBuyer,
                    'credit_limit'=>auth()->user()->credit_limit
                ];
                return response()->json($responseData, 200);

            }else{
                 //Success Response Send
                 $responseData = [
                    'status'   => false,
                    'credit_limit'=>$authUser->credit_limit
                ];
                return response()->json($responseData, 200);
            }


        }catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => $e->getMessage(),
            ];
            return response()->json($responseData, 400);
        }
    }

    public function storeBuyerLikeOrUnlike(Request $request){
        $request->validate([
            'buyer_id' => ['required','numeric'],
            'like'     => ['required','in:0,1'],
            'unlike'   => ['required','in:0,1'],
        ]);

        DB::beginTransaction();
        try {
            $fetchBuyer = Buyer::find($request->buyer_id);
            if($fetchBuyer){
              
                $userId = auth()->user()->id;
                $flag = false;

                $buyerData['liked']      = (int)$request->like;
                $buyerData['disliked']   = (int)$request->unlike;

                $entryExists = UserBuyerLikes::where('user_id',$userId)->where('buyer_id',$fetchBuyer->id)->exists();
                if($entryExists){
                    $buyerData['updated_at'] = Carbon::now();
                    UserBuyerLikes::where('user_id',$userId)->where('buyer_id',$fetchBuyer->id)->update($buyerData);
                    $flag = true;
                }else{
                    $buyerData['user_id']    = $userId;
                    $buyerData['buyer_id']   = $fetchBuyer->id;
                    $buyerData['created_at'] = Carbon::now();
                    UserBuyerLikes::create($buyerData);
                    $flag = true;
                }

                $fetchBuyer->updated_at = \Carbon\Carbon::now();

                $fetchBuyer->save();

                DB::commit();
    
                if($flag){
                    $liked=false;
                    $disliked=false;

                    $getrecentaction=UserBuyerLikes::select('liked','disliked')->where('user_id',$userId)->where('buyer_id',$fetchBuyer->id)->first();
                    if($getrecentaction){
                        $liked=$getrecentaction->liked == 1 ? true : false;
                        $disliked=$getrecentaction->disliked == 1 ? true : false;    
                    }
                                                          
                    $responseData['totalBuyerLikes'] = totalLikes($fetchBuyer->id);
                    $responseData['totalBuyerUnlikes'] = totalUnlikes($fetchBuyer->id);
                    $responseData['liked']=$liked;
                    $responseData['disliked']=$disliked;
                    //Success Response Send
                    $responseData = [
                        'status'   => true,
                        'data'     => $responseData,
                        'message'  => 'Updated Successfully!'
                    ];
                    return response()->json($responseData, 200);

                }else{
                    //Return Error Response
                    $responseData = [
                        'status'        => false,
                        'error'         => trans('messages.error_message'),
                    ];
                    return response()->json($responseData, 400);
                }
               
            }
        }catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }
    }

    public function deleteBuyerLikeOrUnlike(Request $request, $user_id, $buyer_id)
    {
        $validator = Validator::make([
            'user_id' => $user_id,
            'buyer_id' => $buyer_id,
        ], [
            'buyer_id' => ['required', 'numeric'],
            'user_id' => ['required', 'numeric'],
        ]);

       
        DB::beginTransaction();
        try {
            $fetchBuyer = Buyer::find($request->buyer_id);
            if($fetchBuyer){            
                
                $fetchBuyer->updated_at = \Carbon\Carbon::now();

                $fetchBuyer->save();
                
                $flag = false;
                $entryExists = UserBuyerLikes::where('user_id',$user_id)->where('buyer_id',$buyer_id)->exists();               
                if($entryExists){                   
                    UserBuyerLikes::where('user_id',$user_id)->where('buyer_id',$buyer_id)->delete();
                    $flag = true;
                }else{                   
                    $flag = false;
                }
                DB::commit();
    
                if($flag){
                    $responseData['totalBuyerLikes']=UserBuyerLikes::where('buyer_id',$buyer_id)->where('liked',1)->count();
                    $responseData['totalBuyerUnlikes']=UserBuyerLikes::where('buyer_id',$buyer_id)->where('disliked',1)->count();

                    //Success Response Send
                    $responseData = [
                        'status'   => true,
                        'data'     => $responseData,
                        'message'  => 'Updated Successully!'
                    ];
                    return response()->json($responseData, 200);

                }else{
                    //Return Error Response
                    $responseData = [
                        'status'        => false,
                        'error'         => trans('messages.error_message'),
                    ];
                    return response()->json($responseData, 400);
                }
               
            }
        }catch (\Exception $e) {
            DB::rollBack();
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }
    }

    public function myBuyersList(){
        try {
            $radioValues = [0,1];
            $userId = auth()->user()->id;
            
            $buyers = Buyer::query()->select('id','user_id','first_name','last_name','email','phone','created_by','contact_preferance')->where('status', 1)->whereRelation('buyersPurchasedByUser', 'user_id', '=', $userId);

            $buyers = $buyers->orderBy('created_by','desc')->paginate(20);

            foreach ($buyers as $key=>$buyer){
                $liked=false;
                $disliked=false;
                
                $getrecentaction=UserBuyerLikes::select('liked','disliked')->where('user_id',$userId)->where('buyer_id',$buyer->id)->first();
                if($getrecentaction){
                    $liked=$getrecentaction->liked == 1 ? true : false;
                    $disliked=$getrecentaction->disliked == 1 ? true : false;
                }
                
                $name = $buyer->first_name.' '.$buyer->last_name;
                $buyer->name =  $name;

                $buyer->contact_preferance_id = $buyer->contact_preferance;

                $buyer->contact_preferance = $buyer->contact_preferance ? config('constants.contact_preferances')[$buyer->contact_preferance]: '';
                $buyer->redFlag = $buyer->redFlagedData()->where('user_id',$userId)->exists();
                $buyer->totalBuyerLikes = totalLikes($buyer->id);
                $buyer->totalBuyerUnlikes = totalUnlikes($buyer->id);
                $buyer->liked= $liked;
                $buyer->disliked= $disliked;                
                $buyer->redFlagShow = $buyer->buyersPurchasedByUser()->where('user_id',auth()->user()->id)->exists();
                $buyer->createdByAdmin = ($buyer->created_by == 1) ? true : false;
            }
            
            //Return Success Response
            $responseData = [
                'status' => true,
                'buyers' => $buyers,
            ];

            return response()->json($responseData, 200);
        }catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }
    }

    public function searchAddress(Request $request){
        // $search = $request->search;
        try {
            // if($search){
                $buyers = Buyer::query()->select('address','country','state','city','zip_code');

                // $buyers->whereNotNull('address')->where('address','like',$search.'%');
                $buyers->whereNotNull('address');
            
                $buyers = $buyers->get();

                $allBuyers = [];
                foreach($buyers as $key=>$buyer){
                    $labels = '';
                    $labels = $buyer->address;

                    if($buyer->city){
                        // $cityArray = DB::table('cities')->whereIn('id',$buyer->city)->pluck('name')->toArray();
                        // $labels .= count($cityArray) > 0 ?  ', '.implode(',',$cityArray) : '';
                    }

                    if($buyer->state){
                        // $stateArray = DB::table('states')->whereIn('id',$buyer->state)->pluck('name')->toArray();
                        // $labels .= count($stateArray) > 0 ? ', '.implode(', ',$stateArray):'';
                    }

                    $labels .= $buyer->zip_code ? ', '.$buyer->zip_code : '';

                    $allBuyers[0][$labels]['address'] = '';
                    $allBuyers[0][$labels]['city'] = '';
                    $allBuyers[0][$labels]['state'] = '';
                    $allBuyers[0][$labels]['zip_code'] = '';

                    $allBuyers[0][$labels]['address'] = $buyer->address;

                    if($buyer->city){
                        $allBuyers[0][$labels]['city'] = collect($buyer->city)->map(function ($id) {
                            $cityName = DB::table('cities')->where('id',$id)->value('name');
                            return [
                                'value' => $id,
                                'label' => ucfirst(strtolower($cityName)),
                            ];
                        })->values()->all();
                    }

                    if($buyer->state){
                        $allBuyers[0][$labels]['state'] = collect($buyer->state)->map(function ($id) {
                            $stateName = DB::table('states')->where('id',$id)->value('name');
                            return [
                                'value' => $id,
                                'label' => ucfirst(strtolower($stateName)),
                            ];
                        })->values()->all();
                    }

                    $allBuyers[0][$labels]['zip_code'] = $buyer->zip_code;
                }
               
                //Return Error Response
                $responseData = [
                    'status' => true,
                    'result' => $allBuyers,
                ];
                return response()->json($responseData, 200);
            // }
        }catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());
            
            //Return Error Response
            $responseData = [
                'status'        => false,
                'error'         => trans('messages.error_message'),
            ];
            return response()->json($responseData, 400);
        }

    }
}
