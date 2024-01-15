@extends('layouts.main.app')
@section('head')
@include('layouts.main.headersection',[
'title'=> __('Coupons'),
'buttons'=>[
   [
      'name'=>'<i class="fa fa-plus"></i>&nbsp'.__('Create Coupons'),
      'url'=>'#',
      'components'=>'data-toggle="modal" data-target="#addRecord" id="add_record"',
      'is_button'=>true
   ]
]

])
@endsection
@section('content')
<div class="row">
	<div class="col">
		<div class="card">
			<!-- Card header -->
			<div class="card-header border-0">
				<h3 class="mb-0">{{ __('Coupons') }}</h3>
			</div>
			<!-- Light table -->
			<div class="table-responsive">
				<table class="table align-items-center table-flush">
					<thead class="thead-light">
						<tr>
							<th class="col-3">{{ __('Code') }}</th>
							<th class="col-6">{{ __('Discount') }}</th>
							<th class="col-1 text-right">{{ __('Plan') }}</th>
							<th class="col-1 text-right">{{ __('Action') }}</th>
						</tr>
					</thead>					
						@foreach($coupons ?? [] as  $coupon)
						<tr>
							<td class="text-left">
								<!-- {{ Str::limit($coupon->coupon_code,30) }}
                            {} -->
                            {{$coupon->coupon_code}}
							</td>
							<td class="text-left">
								<!-- {{ Str::limit($coupon->excerpt->value ?? '',70) }} -->
                            {{$coupon->discount}} %

							</td>
							<td class="text-right">
                            {{ $coupon->plan->title }}
							</td>
							<td class="text-right">
								<div class="btn-group mb-2 float-right">
									<button class="btn btn-neutral btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										{{ __('Action') }}
									</button>
									<div class="dropdown-menu">
										<a class="dropdown-item has-icon edit-row" href="#" 
										data-action="{{ route('admin.faq.update',$coupon->id) }}" 
										data-question="{{ $coupon->coupon_code ?? '' }}"
										data-answer="{{ $coupon->discount ?? '' }}"  
										data-position="{{ $coupon->position ?? '' }}"  
										data-toggle="modal" 
										data-target="#editModal"
										>
										<i class="fi fi-rs-edit"></i>{{ __('Send on Whatsapp') }}</a>
									</div>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>				
			</div>

			<div class="card-footer py-4">
			</div>					
		</div>
	</div>
</div>




<div class="modal fade" id="addRecord" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <form method="POST" action="{{ route('admin.coupouns.store') }}" class="ajaxform_instant_reload">
            @csrf
            <div class="modal-header">
               <h3>{{ __('Create Coupons') }}</h3>
            </div>
            <div class="modal-body">
               <div class="form-group">
                  <label>{{ __('Coupon Code ') }}</label>
                  <input type="text" name="coupon_code" id="couponCodeInput" class="form-control" required="">
               </div>
               <div class="form-group">
                  <label>{{ __('Coupon %') }}</label>
                  <input type="text" name="discount" class="form-control" required="">
               </div>
               
               <div class="form-group">
                  <label>{{ __('Select subscription') }}</label>
                 <select class="form-control" name="plan_id"  required="">                 	
                 	<option value="top">{{ __('For App Subscription') }}</option>
                     @foreach($plans ?? [] as  $plan)
                 	<option  value="{{ $plan->id }}" selected="">{{ $plan->title }}</option>  
					@endforeach
                 </select>
               </div>
               
            </div>
            <div class="modal-footer m-0">
            <button type="button" class="btn btn-outline-primary col-12 generate-code-button">{{ __('Generate Code') }}</button>
            </div>
            <div class="modal-footer">
               <button type="submit" class="btn btn-outline-primary col-12 submit-button" >{{ __('Create Now') }}</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <form method="POST" action="" id="editForm" >
            <!-- @csrf -->
            <!-- @method("PUT") -->

            <div class="modal-header">
               <h3>{{ __('Edit FAQ') }}</h3>
            </div>
            <div class="modal-body">
               <div class="form-group">
                  <label>{{ __('Coupon Code') }}</label>
                  <input type="text" name="coupon_code"  class="form-control" id="question" >
               </div>
               <div class="form-group">
                  <label>{{ __('Discount') }}</label>
                  <input type="text" name="discount"  class="form-control" id="answer" >
               </div>
               
               <div class="form-group">
                  <label>{{ __('Select subscription') }}</label>
                 <select class="form-control" name="plan_id"   >                 	
                 	<option value="top">{{ __('For App Subscription') }}</option>
                     @foreach($plans ?? [] as  $plan)
                 	<option  value="{{ $plan->id }}" selected="">{{ $plan->title }}</option>  
					@endforeach
                 </select>
               </div>

               <div class="form-group">
                  <label>{{ __('Whatsapp Number') }}</label>
                  <input type="text" name="whatsapp"  class="form-control" id="whatsapp" >
               </div>

            </div>
          
         </form>
         <!-- <a id="whatsappLink" class="btn btn-outline-success col-12" target="_blank" style="display:none;">Open WhatsApp</a> -->
         <div class="modal-footer">
               <!-- <button type="submit" class="btn btn-outline-primary col-12 submit-button" >{{ __('Send on Whatsapp') }}</button> -->
               <a id="whatsappLink" class="btn btn-outline-success col-12" target="_blank">Open WhatsApp</a>

            </div>
      </div>
   </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        $(".generate-code-button").click(function () {
            var characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            var couponCode = '';
            for (var i = 0; i < 12; i++) {
                couponCode += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            $("#couponCodeInput").val(couponCode);
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Assuming your form has an ID of 'editForm'
        var form = document.getElementById('editForm');

        // Add an event listener to the anchor tag click
        document.getElementById('whatsappLink').addEventListener('click', function (event) {
            // Prevent the default behavior of the anchor tag
            event.preventDefault();

            // Get the WhatsApp number and coupon code from the input fields
            var whatsappNumber = document.getElementById('whatsapp').value;
            var couponCode = document.getElementById('question').value;

            // Create the WhatsApp message with the coupon code
            var message = 'Coupon Code for your subscription: ' + couponCode;

            // Open WhatsApp Web using the URL scheme
            var whatsappLink = 'https://wa.me/' + encodeURIComponent(whatsappNumber) + '?text=' + encodeURIComponent(message);

            // Open the WhatsApp link in a new tab or window
            window.open(whatsappLink, '_blank');
        });
    });
</script>
<script src="{{ asset('assets/js/pages/admin/faq.js') }}"></script>
@endpush