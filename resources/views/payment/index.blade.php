<div class="type-effect">
        <div class="text" id="type-text"></div>
      </div>
      <div class="row">
            <div class="col-12 p-5">
                <img src="https://cdn.dribbble.com/users/1281958/screenshots/4862251/waiting-for-pay.gif" width="260px" alt="">
            </div>
        </div>


<form method="post" action="{{$data['redirectUrl']}}" id="form1" name="form1">
<input type="hidden" name="signature" value="{{$signature}}">
<input type="hidden" name="command" value="PURCHASE">
<input type="hidden" name="merchant_reference" value="{{$merchant_reference}}">
<input type="hidden" name="amount" value="{{$amount }}">
<input type="hidden" name="access_code" value ="{{ $data['access_code']}}">
<input type="hidden" name="merchant_identifier" value="{{$data['merchant_identifier']}}">
<input type="hidden" name="currency" value="{{$data['currency']}}">
<input type="hidden" name="language" value="{{$data['language']}}">
<input type="hidden" name="customer_email" value="{{$user['email']}}">
<input type="hidden" name="return_url" value="{{$data['return_url']}}">
<div class="alert alert-primary" role="alert">
  إصغط هنا للشحن
  <input type="submit" class="btn btn-success" value="" id="" name="">
</div>
       </form>
