<?php


if( !isset( $Purchase['amount'] ) ) exit('Missing purchase total amount (price)');
if( !isset( $Purchase['product'] ) ) exit('Missing product to purchase');





?>

<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4" style="margin-left:auto;margin-right:auto;float:none;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><img class="pull-right" src="http://i76.imgup.net/accepted_c22e0.png">Payment Details</h3>
        </div>
        <div class="panel-body">
            <form role="form" id="payment-form">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="cardNumber">CARD NUMBER</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="cardNumber" placeholder="Valid Card Number" required autofocus data-stripe="number" />
                                <span class="input-group-addon" style="opacity: 0.75;"><i class="fa fa-credit-card"></i></span>
                            </div>
                        </div>                            
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-7 col-md-7">
                        <div class="form-group">
                            <label for="expMonth">EXPIRATION DATE</label>
                            <div class="col-xs-6 col-lg-6 pl-ziro">
                                <input type="text" class="form-control" name="expMonth" placeholder="MM" required data-stripe="exp_month" />
                            </div>
                            <div class="col-xs-6 col-lg-6 pl-ziro">
                                <input type="text" class="form-control" name="expYear" placeholder="YY" required data-stripe="exp_year" />
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-5 col-md-5 pull-right">
                        <div class="form-group">
                            <label for="cvCode">CV CODE</label>
                            <input type="password" class="form-control" name="cvCode" placeholder="CV" required data-stripe="cvc" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="couponCode">COUPON CODE</label>
                            <input type="text" class="form-control" name="couponCode" />
                        </div>
                    </div>                        
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button class="btn btn-dark btn-md btn-block" style="float:left;width:49%;" type="button" onclick="window.location.href=window.location.href;window.location.reload();"><span class="fa fa-remove"></span> Cancel</button>
                    
                        <button class="btn btn-success btn-md btn-block" style="float:left;width:49%;  margin-left: 2%;margin-top:0;" type="submit">Continue <span class="fa fa-angle-double-right"></span></button>
                    </div>
                </div>
                <div class="row" style="display:none;">
                    <div class="col-xs-12">
                        <p class="payment-errors"></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

