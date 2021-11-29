@extends('layouts.app')
@section('title') @if( ! empty($title)) {{ $title }} | @endif @parent @endsection

@section('content')
    <section class="campaign-details-wrap">
        @include('single_campaign_header')
        <div class="container">

            <div class="row">
                <div class="col-md-8 col-md-offset-2">

                    <div class="checkout-wrap">

                        <div class="contributing-to">
                            <p class="contributing-to-name"><strong> @lang('app.you_are_contributing_to') {{$campaign->user->name}}</strong></p>
                            <h3>{{$campaign->title}}</h3>
                        </div>

                        <hr />

                        <?php
                        $currency = get_option('currency_sign');
                        ?>

                        <div class="row">
                            @if(get_option('enable_stripe') == 1)
                                <div class="col-md-4">
                                    <div class="stripe-button-container">
                                        <script
                                                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                                                data-key="{{ get_stripe_key() }}"
                                                data-amount="{{ get_stripe_amount(session('cart.amount'))}}"
                                                data-email="{{session('cart.email')}}"
                                                data-name="{{ get_option('site_name') }}"
                                                data-description="{{ $campaign->title." Contributing" }}"
                                                data-currency="{{$currency}}"
                                                data-image="{{ asset('assets/images/stripe_logo.jpg') }}"
                                                data-locale="auto">
                                        </script>
                                    </div>
                                </div>
                            @endif

                            @if(get_option('enable_paypal') == 1)
                                <div class="col-md-4">
                                    <form action="{{route('payment_paypal_receive')}}" method="post"> @csrf

                                        <input type="hidden" name="cmd" value="_xclick" />
                                        <input type="hidden" name="no_note" value="1" />
                                        <input type="hidden" name="lc" value="UK" />
                                        <input type="hidden" name="currency_code" value="{{get_option('currency_sign')}}" />
                                        <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest" />
                                        <button type="submit" class="btn btn-info"> <i class="fa fa-paypal"></i> @lang('app.pay_with_paypal')</button>
                                    </form>
                                </div>
                            @endif


                            @if(get_option('enable_bank_transfer') == 1)
                                <div class="col-md-4">
                                    <button class="btn btn-primary" id="bankTransferBtn"><i class="fa fa-bank"></i> @lang('app.pay_with_bank_bank_transfer')</button>
                                </div>
                            @endif
                        </div>

                        @if(get_option('enable_bank_transfer') == 1)
                            <div class="bankPaymetWrap" style="display: none;">

                                <div class="row">
                                    <div class="col-md-8 col-md-offset-2">


                                        <div class="alert alert-info">
                                            <h4> @lang('app.campaign_unique_info') #{{$campaign->id}} </h4>
                                        </div>

                                        <div class="jumbotron">
                                            <h4>@lang('app.bank_payment_instruction')</h4>

                                            <table class="table">
                                                <tr>
                                                    <th>@lang('app.bank_swift_code')</th>
                                                    <td>{{get_option('bank_swift_code') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('app.account_number')</th>
                                                    <td>{{get_option('account_number') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('app.branch_name')</th>
                                                    <td>{{get_option('branch_name') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('app.branch_address')</th>
                                                    <td>{{get_option('branch_address') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('app.account_name')</th>
                                                    <td>{{get_option('account_name') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>@lang('app.iban')</th>
                                                    <td>{{get_option('iban') }}</td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div id="bankTransferStatus"></div>

                                        <form action="{{route('bank_transfer_submit')}}" id="bankTransferForm" class="form-horizontal" method="post" enctype="multipart/form-data" > @csrf


                                            <div class="form-group {{ $errors->has('bank_swift_code')? 'has-error':'' }}">
                                                <label for="bank_swift_code" class="col-sm-4 control-label">
                                                    @lang('app.bank_swift_code') <span class="field-required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="bank_swift_code" value="{{ old('bank_swift_code') }}" name="bank_swift_code" placeholder="@lang('app.bank_swift_code')">
                                                    {!! $errors->has('bank_swift_code')? '<p class="help-block">'.$errors->first('bank_swift_code').'</p>':'' !!}
                                                </div>
                                            </div>

                                            <div class="form-group {{ $errors->has('account_number')? 'has-error':'' }}">
                                                <label for="account_number" class="col-sm-4 control-label">@lang('app.account_number') <span class="field-required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="account_number" value="{{ old('account_number') }}" name="account_number" placeholder="@lang('app.account_number')">
                                                    {!! $errors->has('account_number')? '<p class="help-block">'.$errors->first('account_number').'</p>':'' !!}
                                                </div>
                                            </div>

                                            <div class="form-group {{ $errors->has('branch_name')? 'has-error':'' }}">
                                                <label for="branch_name" class="col-sm-4 control-label">@lang('app.branch_name') <span class="field-required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="branch_name" value="{{ old('branch_name') }}" name="branch_name" placeholder="@lang('app.branch_name')">
                                                    {!! $errors->has('branch_name')? '<p class="help-block">'.$errors->first('branch_name').'</p>':'' !!}
                                                </div>
                                            </div>

                                            <div class="form-group {{ $errors->has('branch_address')? 'has-error':'' }}">
                                                <label for="branch_address" class="col-sm-4 control-label">@lang('app.branch_address') <span class="field-required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="branch_address" value="{{ old('branch_address') }}" name="branch_address" placeholder="@lang('app.branch_address')">
                                                    {!! $errors->has('branch_address')? '<p class="help-block">'.$errors->first('branch_address').'</p>':'' !!}
                                                </div>
                                            </div>

                                            <div class="form-group {{ $errors->has('account_name')? 'has-error':'' }}">
                                                <label for="account_name" class="col-sm-4 control-label">@lang('app.account_name') <span class="field-required">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="account_name" value="{{ old('account_name') }}" name="account_name" placeholder="@lang('app.account_name')">
                                                    {!! $errors->has('account_name')? '<p class="help-block">'.$errors->first('account_name').'</p>':'' !!}
                                                </div>
                                            </div>

                                            <div class="form-group {{ $errors->has('iban')? 'has-error':'' }}">
                                                <label for="iban" class="col-sm-4 control-label">@lang('app.iban')</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" id="iban" value="{{ old('iban') }}" name="iban" placeholder="@lang('app.iban')">
                                                    {!! $errors->has('iban')? '<p class="help-block">'.$errors->first('iban').'</p>':'' !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-4 col-sm-8">
                                                    <button type="submit" class="btn btn-primary">@lang('app.pay')</button>
                                                </div>
                                            </div>

                                        </form>


                                    </div>
                                </div>

                            </div>
                        @endif

                    </div>

                </div>

            </div>

        </div>

    </section>

@endsection

@section('page-js')

    <script>
        $(function() {
            $('.stripe-button').on('token', function(e, token){
                $('#stripeForm').replaceWith('');

                $.ajax({
                    url : '{{route('payment_stripe_receive')}}',
                    type: "POST",
                    data: { stripeToken : token.id, _token : '{{ csrf_token() }}' },
                    success : function (data) {
                        if (data.success == 1){
                            $('.checkout-wrap').html(data.response);
                            toastr.success(data.msg, '@lang('app.success')', toastr_options);
                        }
                    }
                });
            });

            @if(get_option('enable_bank_transfer') == 1)

            $('#bankTransferBtn').click(function(){
                $('.bankPaymetWrap').slideToggle();
            });

            $('#bankTransferForm').submit(function(e){
                e.preventDefault();

                var form_input = $(this).serialize()+'&_token={{csrf_token()}}';

                $.ajax({
                    url : '{{route('bank_transfer_submit')}}',
                    type: "POST",
                    data: form_input,
                    success : function (data) {
                        if (data.success == 1){
                            $('.checkout-wrap').html(data.response);
                            toastr.success(data.msg, '@lang('app.success')', toastr_options);
                        }
                    },
                    error   : function ( jqXhr, json, errorThrown ) {
                        var errors = jqXhr.responseJSON;
                        var errorsHtml= '';
                        $.each( errors, function( key, value ) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        toastr.error( errorsHtml , "Error " + jqXhr.status +': '+ errorThrown);
                    }
                });

            });
            @endif

        });
    </script>

@endsection