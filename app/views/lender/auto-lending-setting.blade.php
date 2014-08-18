@extends('layouts.master')

@section('page-title')
Account Preferences
@stop

@section('content')
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="page-header">
            <h1>Automated Lending</h1>
        </div>
        <div>
        <p>
        Automated lending allows you to maximize your impact by continuously relending your available lending credit. Talk about paying it forward! <a href="#">Learn more</a>
        </p>
        </div>        
        {{ BootstrapForm::open(array('route' => 'lender:post:auto-lending', 'translationDomain' => 'lender.auto-lending.preferences')) }}
        {{--{{ BootstrapForm::populate($form) }}--}}
        <div>
        {{ BootstrapForm::($form) }}
        <input type="radio" id="status_active" name="status" value="1">YES!  Activate automated lending.
        <input type="radio" id="status_inactive" checked="true" name="status" value="0">No thanks, deactivate automated lending.
        </div>
        {{ BootstrapForm::submit('save') }}
        {{ BootstrapForm::close() }}
    </div>
</div>
@stop

<tbody>
					<!-- <tr><td><strong class='subhead' >Automated Lending Preferences</strong></td></tr> -->
					<tr>
						<td><input type="radio" id="status_active" name="status" value="1">YES!  Activate automated lending.</td>
					</tr>
					<tr>
						<td><input type="radio" id="status_inactive" checked="true" name="status" value="0">No thanks, deactivate automated lending.</td>
					</tr>
					<tr>
						<td></td><td></td>
					</tr>
					<tr height="25px"><td colspan="3"></td></tr>
					<tr>
						<td>
							 Please set your minimum desired interest rate.
							<a style="cursor:pointer" class="tt">
								<img src="library/tooltips/help.png" style="border-style: none;">
								<span class="tooltip">
								<span class="top"></span>
									<span class="middle">
										This is the minimum interest rate at which your available balance will be lent out.  If there are no fundraising loan applications that are offering your minimum interest rate, then your balance will not be lent.
									</span>
									<span class="bottom"></span>
								</span>
							</a>
						</td>
					</tr>
					<tr>
						<td><input id="interest_rate1" type="radio" checked="true" name="interest_rate" value="0" onclick="ResetOther()">0%</td>
					</tr>
					<tr>
						<td><input id="interest_rate2" type="radio" name="interest_rate" value="3" onclick="ResetOther()">3%</td>
					</tr><tr>
						<td><input id="interest_rate3" type="radio" name="interest_rate" value="5" onclick="ResetOther()">5%</td>
					</tr>
					<tr>
						<td><input id="interest_rate4" type="radio" name="interest_rate" value="10" onclick="ResetOther()">10%</td>
					</tr>
					<tr>
						<td>
							<input type="radio" checked="" name="interest_rate" value="" id="InterestRateOther">Other
							<span id="otheramount" style="margin-left:10px;">
								<input type="text" id="desired_interest_rate" onfocus="setChecked()" name="interest_rate_other" value="">
								<br>
								<div id="interest_rate_err" style="color: red;"></div>
							</span>
						</td>
					</tr>
						<tr height="25px"><td colspan="3"></td></tr>
					<tr>
						<td>
							Please set your maximum desired interest rate.
							<a style="cursor:pointer" class="tt">
								<img src="library/tooltips/help.png" style="border-style: none;">
								<span class="tooltip">
								<span class="top"></span>
									<span class="middle">
										This is the maximum interest rate at which your available balance will be lent.
									</span>
									<span class="bottom"></span>
								</span>
							</a>
						</td>
					</tr>
					<tr>
						<td><input id="max_interest_rate1" type="radio" checked="true" name="max_interest_rate" value="0" onclick="ResetOtherMax()">0%</td>
					</tr>
					<tr>
						<td><input id="max_interest_rate2" type="radio" name="max_interest_rate" value="3" onclick="ResetOtherMax()">3%</td>
					</tr><tr>
						<td><input id="max_interest_rate3" type="radio" name="max_interest_rate" value="5" onclick="ResetOtherMax()">5%</td>
					</tr>
					<tr>
						<td><input id="max_interest_rate4" type="radio" name="max_interest_rate" value="10" onclick="ResetOtherMax()">10%</td>
					</tr>
					<tr>
						<td>
							
							<input type="radio" checked="" name="max_interest_rate" value="" id="MaxInterestRateOther">Other
							<span id="otheramount" style="margin-left:10px;">
								<input type="text" id="max_desired_interest_rate" onfocus="setCheckedMax()" name="max_interest_rate_other" value="">
								<br>
								<div id="max_interest_rate_err"></div>
							</span>
						</td>
					</tr>
					<tr height="25px"><td colspan="3"></td></tr>
					<tr>
						<td>
							 How would you like your funds to be automatically lent out?
							<a style="cursor:pointer" class="tt">
								<img src="library/tooltips/help.png" style="border-style: none;">
								<span class="tooltip">
									<span class="top"></span>
									<span class="middle">
										Your funds will be allocated automatically to loan applications that meet the criteria you specify here. In the event that more than one loan meets the specified criteria, a loan will be chosen at random from among them for each $10 lent.</span>
									<span class="bottom"></span>
								</span>
							</a>
						</td>
					</tr>
					<tr style="margin-top:20px;">
						<td><input id="priority1" type="radio" name="priority" checked="true" value="1">Give priority to borrowers with highest feedback rating.</td>
					</tr>
					<tr>
						<td><input id="priority2" type="radio" name="priority" value="2">Give priority to loans expiring the soonest.</td>
					</tr>
					<tr>
						<td><input id="priority3" type="radio" name="priority" value="3">Give priority to loans with highest available interest rates.</td>
					</tr>
					<tr>
						<td><input id="priority4" type="radio" name="priority" value="4">Give priority to borrowers with the highest number of comments posted.</td>
					</tr>
					<tr>
						<td>
							<input id="priority5" type="radio" name="priority" value="5">Choose loans at random.<br>
														</td>
					</tr>
					
					<tr>
						<td>
							<input id="priority6" type="radio" name="priority" value="6">Match loans made manually by other lenders.<br><br><br>
														</td>
					</tr>
					
					<tr height="25px"><td colspan="3"></td></tr>
										<tr style="display:none">
						<td>
							 Would you like your current credit balance of USD 0.00 to be automatically allocated to fundraising loans according to these criteria?
						</td>
					</tr>
					
					<tr style="display:none">
						<td><input id="currnt_allocated_yes" type="radio" checked="true" name="confirm_criteria" value="1">Yes, apply automated lending to both my current balance and to future repayments that are credited to my account.</td>
					</tr>
					<tr style="display:none">
						<td><input id="currnt_allocated_no" type="radio" name="confirm_criteria" value="0">No, apply automated lending only to future repayments and leave my current balance available for manual lending.</td>
					</tr>
						<tr height="25px" style="display:none"><td colspan="3"></td></tr>
					<tr>
						<input type="hidden" name="automaticLending">
						<input type="hidden" name="user_guess" value="14e9df85436e53a758ec1b7b0642de53">
						<td><input type="submit" name="save_preference" value="Save Preferences" onclick="needToConfirm = false;" class="btn"></td>
					</tr>

				</tbody>
				
@section('script-footer')
<script type="text/javascript">
    $('.karmaScore').tooltip({placement: 'bottom', title: 'Karma is calculated on the basis of the total amount lent by the new members a member has recruited to Zidisha via email invites or gift cards, and the number of comments a member has posted in the Zidisha website.'})
</script>
@stop
