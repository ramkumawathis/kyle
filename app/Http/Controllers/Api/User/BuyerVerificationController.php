<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\Controller;

class BuyerVerificationController extends Controller
{
    public function index(Request $request){
    
        switch ($request->step) {
            case 1:
                return $this->phoneVerification($request);
            break;
            case 2:
                return $this->driverLicenseVerification($request);
            break;
            case 3:
                return $this->proofOfFundsVerification($request);
            break;
            case 4:
                return $this->LLCVerification($request);
            break;
            case 5:
                return $this->applicationProcess($request);
            break;
            default:
               //Return Error Response
                $responseData = [
                    'status'        => false,
                    'error'         => trans('messages.error_message'),
                ];
                return response()->json($responseData, 400);
        }

    }

    private function phoneVerification($request){
        $userId = auth()->user()->id;

        $rules['phone'] = ['required', 'numeric','not_in:-','unique:users,phone,'.$userId.',id,deleted_at,NULL'];
        $rules['otp1'] = ['required','numeric','digits:1','not_in:-'];
        $rules['otp2'] = ['required','numeric','digits:1','not_in:-'];
        $rules['otp3'] = ['required','numeric','digits:1','not_in:-'];
        $rules['otp4'] = ['required','numeric','digits:1','not_in:-'];

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $otpNumber = (int)$request->otp1.$request->otp2.$request->otp3.$request->otp4;
            
            $otpVerify = User::where('id',$userId)->where('otp',$otpNumber)->first();
            if($otpVerify){
                $otpVerify->otp = null;
                $otpVerify->phone = $request->phone;
                $otpVerify->phone_verified_at = date('Y-m-d H:i:s');
                $otpVerify->save();
                $otpVerify->buyerVerification()->update(['is_phone_verification'=>1]);

                DB::commit();
                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'current_step'  => $request->step,
                    'message'       => trans('messages.auth.verification.phone_verify_success'),
                ];
                return response()->json($responseData, 200);
            }else{
                //Return Error Response
                $responseData = [
                    'status'        => false,
                    'error'         => trans('messages.auth.verification.invalid_otp'),
                ];
                return response()->json($responseData, 400);
            }
        }catch (\Exception $e) {
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

    private function driverLicenseVerification($request){
       
        /**
         * Rules
         * image|mimes:jpeg,png,gif|max:2048|dimensions:min_width=800,min_height=600
         */
        $rules['driver_license_front_image'] = ['required','image','mimes:jpeg,jpg','max:2048'];
        $rules['driver_license_back_image']  = ['required','image','mimes:jpeg,jpg','max:2048'];

        $customMessage = [];

        $attributNames = [
            'driver_license_front_image' => 'front id photo',
            'driver_license_back_image' => 'back id photo',
        ];

        $request->validate($rules,$customMessage,$attributNames);

        DB::beginTransaction();
        try {
            $userId = auth()->user()->id;
            $user = User::where('id',$userId)->first();
            if($user){

                // Start front image upload
                $uploadFrontId = null;
                if ($request->driver_license_front_image) {
                    if($user->driverLicenseFrontImage){
                        $uploadFrontId = $user->driverLicenseFrontImage->id;
                        uploadImage($user, $request->driver_license_front_image, 'buyer/verification/',"driver-license-front", 'original', 'update', $uploadFrontId);
                    }else{
                        uploadImage($user, $request->driver_license_front_image, 'buyer/verification/',"driver-license-front", 'original', 'save', $uploadFrontId);
                    }
                }

                // Start back image upload
                $uploadBackId = null;
                if ($request->driver_license_back_image) {
                    if($user->driverLicenseBackImage){
                        $uploadBackId = $user->driverLicenseBackImage->id;
                        uploadImage($user, $request->driver_license_back_image, 'buyer/verification/',"driver-license-back", 'original', 'update', $uploadBackId);
                    }else{
                        uploadImage($user, $request->driver_license_back_image, 'buyer/verification/',"driver-license-back", 'original', 'save', $uploadBackId);
                    }
                }

                $user->buyerVerification()->update(['is_driver_license'=>1]);

                DB::commit();

                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'current_step'  => $request->step,
                    'message'       => trans('messages.auth.verification.driver_license_success'),
                ];
                return response()->json($responseData, 200);
            }
        }catch (\Exception $e) {
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

    private function proofOfFundsVerification($request){
        /**
         * Rules
         * file|mimes:pdf|max:5120|
        */
        $rules['bank_statement_pdf']   = ['required','file','mimes:pdf'];
        $rules['other_proof_of_fund']  = ['required','string'];

        $customMessage = [];

        $attributNames = [
            'bank_statement_pdf' => 'bank statement',
        ];

        $request->validate($rules,$customMessage,$attributNames); 

        DB::beginTransaction();
        try {
            $userId = auth()->user()->id;
            $user = User::where('id',$userId)->first();
            if($user){

                // Start bank statement pdf upload
                $uploadId = null;
                if($request->bank_statement_pdf) {
                    if($user->bankStatementPdf){
                        $uploadId = $user->bankStatementPdf->id;
                        uploadImage($user, $request->bank_statement_pdf, 'buyer/verification/',"bank-statement-pdf", 'original', 'update', $uploadId);
                    }else{
                        uploadImage($user, $request->bank_statement_pdf, 'buyer/verification/',"bank-statement-pdf", 'original', 'save', $uploadId);
                    }
                }

                $user->buyerVerification()->update(['other_proof_of_fund'=>$request->other_proof_of_fund,'is_proof_of_funds'=>1]);

                DB::commit();

                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'current_step'  => $request->step,
                    'message'       => trans('messages.auth.verification.proof_of_funds_success'),
                ];
                return response()->json($responseData, 200);
            }
        }catch (\Exception $e) {
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

    private function LLCVerification($request){
        /**
         * Rules
         * image|mimes:jpeg,png,gif|max:2048|dimensions:min_width=800,min_height=600
        */
        $rules['llc_front_image'] = ['required','image','mimes:jpeg,jpg','max:2048'];
        $rules['llc_back_image']  = ['required','image','mimes:jpeg,jpg','max:2048'];

        $customMessage = [];

        $attributNames = [
            'llc_front_image' => 'front id photo',
            'llc_back_image'  => 'back id photo',
        ];

        $request->validate($rules,$customMessage,$attributNames);

        DB::beginTransaction();
        try {
            $userId = auth()->user()->id;
            $user = User::where('id',$userId)->first();
            if($user){

                // Start front image upload
                $uploadFrontId = null;
                if ($request->llc_front_image) {
                    if($user->llcFrontImage){
                        $uploadFrontId = $user->llcFrontImage->id;
                        uploadImage($user, $request->llc_front_image, 'buyer/verification/',"llc-front-image", 'original', 'update', $uploadFrontId);
                    }else{
                        uploadImage($user, $request->llc_front_image, 'buyer/verification/',"llc-front-image", 'original', 'save', $uploadFrontId);
                    }
                }

                // Start back image upload
                $uploadBackId = null;
                if ($request->llc_back_image) {
                    if($user->llcBackImage){
                        $uploadBackId = $user->llcBackImage->id;
                        uploadImage($user, $request->llc_back_image, 'buyer/verification/',"llc-back-image", 'original', 'update', $uploadBackId);
                    }else{
                        uploadImage($user, $request->llc_back_image, 'buyer/verification/',"llc-back-image", 'original', 'save', $uploadBackId);
                    }
                }

                $user->buyerVerification()->update(['is_llc_verification'=>1]);
 
                DB::commit();

                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'current_step'  => $request->step,
                    'message'       => trans('messages.auth.verification.llc_verification_success'),
                ];
                return response()->json($responseData, 200);
            }
        }catch (\Exception $e) {
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

    private function applicationProcess(){
        DB::beginTransaction();
        try {
            $userId = auth()->user()->id;
            $user = User::where('id',$userId)->first();
            if($user){
                $user->buyerVerification->is_application_process = 1;
                $user->save();

                DB::commit();

                //Return Success Response
                $responseData = [
                    'status'        => true,
                    'current_step'  => $request->step,
                    'message'       => trans('messages.auth.verification.application_process_success'),
                ];
                return response()->json($responseData, 200);
            }
        }catch (\Exception $e) {
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

    public function getLastVerificationForm(){
        $lastStepForm = 0;
        $user = auth()->user();

        if($user->buyerVerification->is_phone_verification){
            $lastStepForm = 1;
        }

        if($user->buyerVerification->is_driver_license){
            $lastStepForm = 2;
        }

        if($user->buyerVerification->is_proof_of_funds){
            $lastStepForm = 3;
        }

        if($user->buyerVerification->is_llc_verification){
            $lastStepForm = 4;
        }

        if($user->buyerVerification->is_application_process){
            $lastStepForm = 5;
        }

        //Return Success Response
        $responseData = [
            'status'        => true,
            'lastStepForm'  => (int)$lastStepForm-1,
        ];
        return response()->json($responseData, 200);
    }
}