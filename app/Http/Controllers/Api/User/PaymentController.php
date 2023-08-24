<?php
namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Event;
use Stripe\Customer;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\Subscription;

class PaymentController extends Controller
{
    public function __construct(Request $request)
    {
        $this->default_currency = config('constants.default_currency');
        $this->currency = $request->currency;

        $this->stripeSecret = Stripe::setApiKey(config('app.stripe_secret_key')); // test stripe pub key
    }

    public function config(){
         try {
         	$responseData = [
                'status'            => true,
                'key'               => config('app.stripe_publishable_key'),
            ];

         	return response()->json($responseData, 200);
         }catch(\Exception $e){
			$responseData = [
			    'status'        => false,
			    'error'         => trans('messages.error_message'),
			];
			return response()->json($responseData, 400);
         }
    }

   public function createPaymentIntent(Request $request){
        Stripe::setApiKey(config('app.stripe_secret_key'));
        $request->validate([
            'plan' => 'required'
        ]);

        try {
            $createPaymentIntent = true;
            $authUser = auth()->user();
            $plan = Plan::where('plan_token',$request->plan)->first();
            if($plan){

                $authUser = auth()->user();

                // Create or retrieve Stripe customer
                if (!$authUser->stripe_customer_id) {
                    $customer = Customer::create([
                        'name'  => $authUser->name,
                        'email' => $authUser->email,
                        // 'payment_method' => $paymentIntentObject['payment_method'],
                    ]);
                    $authUser->stripe_customer_id = $customer->id;
                } else {
                    $customer = Customer::retrieve($authUser->stripe_customer_id);
                }

                if($authUser->stripe_customer_id){
                   // Retrieve the last payment intent for the customer
                    $intents = PaymentIntent::all([
                        'customer' =>  $authUser->stripe_customer_id,
                        'limit' => 1,
                    ]);
                    if(!empty($intents->data)){
                        $lastPaymentIntent = $intents->data[0];
                        if($lastPaymentIntent->status =='incomplete'){
                            $createPaymentIntent = false;
                            $paymentIntent = PaymentIntent::update($lastPaymentIntent->id, [
                                'amount' => (float)$plan->price * 100,
                                'currency' => config('constants.currency'),
                                'customer' => $customer->id,
                                'automatic_payment_methods' => ['enabled' => 'true'],
                            ]);
                        }
                    }

                }
                
                if($createPaymentIntent){
                    $paymentIntent = PaymentIntent::create([
                        'amount' =>(float)$plan->price * 100, // Amount in cents
                        'currency' => config('constants.currency'),
                        'automatic_payment_methods' => ['enabled' => 'true'],
                    ]);
                }
                
                $clientSecret = $paymentIntent ? $paymentIntent->client_secret : null;
                return response()->json(['status'=>true,'client_secret'=>$clientSecret,'message'=>'Success'], 200);
            }else{
                $responseData = [
                    'status'        => false,
                    'error'         => 'Invalid Plan!',
                ];
                return response()->json($responseData, 500);
            }
        }catch(\Exception $e){
            // dd($e->getMessage().'->'.$e->getLine());
           $responseData = [
               'status'        => false,
               'error'         => trans('messages.error_message'),
           ];
           return response()->json($responseData, 400);
        }
   }

   public function createSubscription(Request $request){
        $request->validate([
            'payment_intent' =>'required',
            'payment_intent_client_secret' =>'required',
        ]);
        try {
            Stripe::setApiKey(config('app.stripe_secret_key'));

            if($request->redirect_status == 'succeeded'){

               $paymentIntentObject =  $this->fetchPaymentIntent($request->payment_intent);

               $paymentIntentObject = json_decode($paymentIntentObject->content(),true)['payment_intent'];

                $authUser = auth()->user();

                // Customer::update($authUser->stripe_customer_id, [
                //     'default_payment_method' => $paymentIntentObject['payment_method'],
                // ]);

                // // Create or retrieve Stripe customer
                // if (!$authUser->stripe_customer_id) {
                //     $customer = Customer::create([
                //         'name'  => $authUser->name,
                //         'email' => $authUser->email,
                //         // 'payment_method' => $paymentIntentObject['payment_method'],
                //     ]);
                //     $authUser->stripe_customer_id = $customer->id;
                // } else {
                //     $customer = Customer::retrieve($authUser->stripe_customer_id);
                // }

                $authUser->level_type = 2;
                $authUser->save();

                // $retrievPlan = Plan::where('plan_token',$paymentIntentObject->plan)->first();

                // if( $retrievPlan ){
                //     $subscription = \Stripe\Subscription::create([
                //         'customer' => $customer->id,
                //         'plan' => $retrievPlan->plan_token,
                //     ]);

                //     Subscription::create([
                //         'plan_id'                => $retrievPlan->id,
                //         'stripe_customer_id'     => $customer->id,
                //         'stripe_plan_id'         => $retrievPlan->plan_token,
                //         'stripe_subscription_id' => $subscription->id,
                //         'start_date'             => $subscription->start_date,
                //         'end_date'               => $subscription->end_date,
                //         'subscription_json'      => json_encode($subscription),
                //     ]);
                // }
                
                // Transaction::create([
                //     'user_id'  => $authUser->id,
                //     'amount'   => (float)$paymentIntentObject->amount/100, 
                //     'currency' => config('constants.currency'),
                //     'status'   => $request->redirect_status,
                //     'payment_method'   => $paymentIntentObject->payment_method,
                //     'payment_json'   => json_encode($paymentIntentObject),
                // ]);
              
                return response()->json(['status'=>true,'message' => 'Success']);
            }
        } catch (\Exception $e) {
            // dd($e->getMessage().'->'.$e->getLine());
            return response()->json(['error' => $e->getMessage()], 500);
        }
   }

   public function fetchPaymentIntent($paymentIntentId){
    
    $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

    return response()->json(['status'=>true,'payment_intent'=>$paymentIntent,'message'=>'Success'], 200);

   }

   public function handleStripeWebhook(Request $request){
        // Verify the webhook signature
        $payload = @file_get_contents('php://input');
        $event = Event::constructFrom(json_decode($payload, true));

        // Handle different types of webhook events
        if ($event->type === 'invoice.payment_succeeded') {
            // Handle successful payment
        } elseif ($event->type === 'invoice.payment_failed') {
            // Handle failed payment
        }

        // // Handle the event
        // switch ($event->type) {
        //     case 'payment_intent.succeeded':
        //         $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
        //         // Then define and call a method to handle the successful payment intent.
        //         // handlePaymentIntentSucceeded($paymentIntent);
        //         break;
        //     case 'payment_method.attached':
        //         $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
        //         // Then define and call a method to handle the successful attachment of a PaymentMethod.
        //         // handlePaymentMethodAttached($paymentMethod);
        //         break;
        //     // ... handle other event types
        //     default:
        //         echo 'Received unknown event type ' . $event->type;
        // }

        return response()->json(['status' => 'success']);
   }

}
?>