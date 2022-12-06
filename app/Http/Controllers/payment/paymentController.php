<?php

namespace App\Http\Controllers\payment;

use App\Models\offer\offer;
use Illuminate\Http\Request;
use App\Models\users\customer;
use App\Http\Controllers\Controller;
use App\Models\transactions\chargetransaction;

class paymentController extends Controller
{
    public  function index(offer $offer){

        $user = customer::find(Auth('sanctum')->user()->id);
         $totalCost = offerCost($offer);

         $userBalance  =  $user->currentBalance();

        if ($userBalance >= $totalCost) {
        //    $acceptQutation =  acceptQutation($quotationId);
           $status = 200;
        } else {
          $amount =  $userBalance - $totalCost;
           $status = 100;
           $amount=  abs($amount) ;
           $amount = $amount * 100 ;
          //  echo  $amount = floatval( $amount);
          // int $amount;
          // $amount = (float) $amount;


        }
        if ($status == 100) {
        $data = merchant_helper();
          $merchant_reference = '2e-' . $totalCost . '-pid-' . $offer->id . '-recharge-' . $user->id;
          $requestParams = array(
            'command' => 'PURCHASE',
            'access_code' => $data['access_code'],
            'merchant_identifier' => $data['merchant_identifier'],
            'merchant_reference' => $merchant_reference,
            'amount' => $amount,
            'currency' => $data['currency'],
            'language' => $data['language'],
            'customer_email' => $user->email,
            'return_url' => $data['return_url'],
          );
          ksort($requestParams);
          $shaString = '';
          foreach ($requestParams as $key => $value) {
            $shaString .= "$key=$value";
          }
         $shaString =$data['hash'] . $shaString .$data['hash'];
          $signature = hash("sha256", $shaString);

          return view('payment.index' , ['data'=>$data , 'user'=>$user , 'amount'=>$amount ,'shaString'=>$shaString ,'signature'=>$signature , 'merchant_reference'=>$merchant_reference ]);




        }


        // echo $status;

    }

    public function proccess(){
  $status = 0;
//   return "test";
            $processMerchantPageResponse =  processMerchantPageResponse() ;
               $message =$processMerchantPageResponse['message'] ;
               $response_code =$processMerchantPageResponse['response_code'] ;
               $statusAPI =$processMerchantPageResponse['statusAPI'] ;
               $statusWEB =$processMerchantPageResponse['statusWEB'] ;
               $amount =$processMerchantPageResponse['amount'] ;
               $merchantReference =$processMerchantPageResponse['merchantReference'] ;
               $userAccountId =  strstr($merchantReference,"-recharge-");
               $pid =  strstr($merchantReference,"-pid-");
               $pid =  str_replace('-pid-', '' ,$pid);
             $pid = explode("-recharge-", $pid);
               $pid =  $pid[0] ;
            //    $getSptIdOfQuotation =  getSptIdOfQuotation($pid);
              $offer = offer::find($pid);
             $user=$offer->order->user;
            //    $orderId = $getSptIdOfQuotation['orderId'];
            //    $userAccountId =  getUserAccountIdOfOrder($orderId);
            //    $userAccount = getInfoAccount('customerAccount', $userAccountId);
               $customer_email = $user->email;
               $mobile = $user->mobile;
               $fullName = $user->full_name;
               $userName = $user->userName;

                 $url_send = 'https://ptoolz.com/Controller/transactions/_AcceptAndHoldTransaction.php';
                 $data = array(
                    'userId' =>$user->id ,
                    'quotationId' => $offer->id,
                 ) ;
                // $res =  httpPost($url_send, $data) ;


                $search = '-recharge-' ;
                $userAccountId = str_replace($search, '', $user->id) ;
               if ($message == 'عملية ناجحة') {
                chargetransaction::create([
                    "user_id" => $user->id,
                    "value" =>$amount,
                ]);

$status = 1;
            }
            return view('payment.proccess', compact('message' , 'status' ,'fullName' ,'amount'));
            }


    }

