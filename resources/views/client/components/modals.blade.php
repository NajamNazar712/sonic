@if(Session::has('agreement_signed') && session('agreement_signed') != 1)
    <div class="modal fade text-left" style="overflow-y: auto" id="ShowAgreementModal" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ShowAgreementModal">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{session('error')}}
                    </div>
                @endif
                <div class="modal-header">
                    <h2 class="modal-title" style="margin:0 auto">Services Agreement</h2>
                </div>
               @php
                   if(session('user_type') == 1){
                       $shipper_name = ucfirst(Auth::user()->name);
                       $poc = Auth::user()->poc;
                       $address = Auth::user()->address;

                   }else {
                    $shipper_name = ucfirst(Auth::user()->shipper->name);
                    $poc = Auth::user()->shipper->poc;
                    $address = Auth::user()->shipper->address;

                   }
                    
               @endphp

                <div class="modal-body password_change_body text-justify" id="password_change_body">
                            <div id="crf_agreement" class="px-4"></div>
                            <div class="px-4">
                            <p>This Services Agreement (“Agreement”) is hereby made on {{ date("l") }} day of {{date("d-m-Y")}}, (“Effective Date”) by and between TRAX Online (Private) Limited, a registered private limited company having incorporation number 0111649 and registered address at Plot #4, DMCHS, Block #7/8, Adjacent to IBL Building Centre, Tipu Sultan Road, Karachi duly represented by Mr. Fawad Ahmed s/o Mr. Muhammad Ayub in his capacity as Company Secretary (hereinafter referred to “TRAX” along with its agents, representatives, successors-in-interests, assigns etc) for the first part And <strong>{{$shipper_name}}</strong> a registered private limited company and registered address at <strong>{{$address}}</strong> duly represented by Mr. /Ms. <strong>{{$poc}}</strong>. in his capacity as (hereinafter referred to “Shipper” along with its agents, representatives, successors-in-interests, assigns etc) for the other part</p>
                                <p>TRAX Online and <strong>{{$shipper_name}}</strong>. shall be hereinafter collectively referred to as “Parties” and individually as “Party”. 
                                </p>
                                <hr>
                                <h4>Recitals:</h4>
                                <p> 
                                    Whereas TRAX Online is an E-Commerce Fulfillment Company with a focus on Cash on Delivery & Logistics Services having a respectable clientele and providing services all over Pakistan. Shipper wishes to introduce Cash on Delivery + E-fulfillment services to its customers (referred to as consignees) and hereby wish to indulge TRAX Online in regards to this particular venture. TRAX Online in return, is willing to provide services to the Shipper. 
                                    </p>
                                <hr>

                                    <h6>Now hereinafter witnesseth on the following terms and conditions:</h6>
                                    <br>
                                    <ol>
                                        <li><p><strong>TRAX Online (Pvt) Ltd.</strong> will act as an agent on behalf of the shipper. It shall have complete legal authority to collect the cash and transfer the ownership of goods to the consignee. Copy of NTN Certificate will be required for account activation.</p></li>
                                        <li><p>The financial charges as mentioned in the Services Proposal shall apply in its entirety. Annexure A with all its terms and conditions will be equally applicable along with this Agreement.  Taxes will be applicable on total shipment charges depending on the origin of shipments. 13% GST will be applied to the shipments originating from Sindh. 16% GST will be applied to the shipments originating from Punjab. ….% Fuel Surcharge will be applied. All the rates are subject to change at any time on discretion of Trax. In case of any claim, same will be processed in line with the policy of Trax.</p></li>
                                        <li><p>During the transit, if any government agency like CAA, FIA inspect the shipment for security or regulatory reasons, shipper will be responsible to provide the relevant documents and shall take full responsibility of the shipment, and Shipper and its consignee, where need be, shall provide complete indemnification to Trax against any legal claim whatsoever. Such indemnification shall keep Trax’s right of initiating legal action against Shipper intact.</p></li>
                                        <li><p>Shipper should not move or ship any of the below mentioned items through <strong>TRAX Online</strong> namely Currency, jewelry, Bullion, Antiques, Liquor, Stamps, Precious Metals, Precious Stones, Works of Art, Fire Arms, Plants, Drugs, Explosives, Animals, Perishable goods and items, Negotiable Instruments in bearer form, Lewd Objects, Obscene and Pornographic Material, Industrial Carbons and Diamonds, hazardous or combustible materials, and all other items/articles restricted by IATA (International Air Transport Association), ICAO (International Civil Aviation Organization) and any item whose distribution is regulated by law or by any statute of the Provincial or Federal Government. TRAX will have full legal authority to act against the shipper in case any such item is found in any shipments. In case any such prohibited/fake product is distributed/transferred/shipped/couriered via TRAX Online, indemnification requirement as laid out in Clause 3 above shall apply.</p> </li>
                                        <li><p>In case the consignee of the shipper feels that the product is not up to the quality or description as provided, TRAX Online shall not be responsible in any manner in this regard and shall not be liable to return money and carry the opened package with it, causing cost and inconvenience. In case such an event happen, TRAX Online shall be at liberty to pass on the direct contact of shipper along with its contact details enabling the consignee to launch a direct complaint to the shipper. Moreover, shipper should enable a change/return policy with a reasonable number of days and should arrange its own transport for picking up the complaint package. Such information of shipper can be shared in case any adverse actions such as assault, battery or legal action is taken against TRAX Online or any of its employees by the consignees of the shipper.</p></li>
                                        <li><p>Any notice, demand, request, consent, agreement or approval which may or is required to be given pursuant to this Agreement shall be in writing and shall be sufficiently given or made if served personally upon the party or a representative or officer of the party for whom it is intended, or mailed by certified or registered mail, postage prepaid, or telexed, telegraphed, or tele copied, addressed at such address to such officers as a party may from time to time advise to the other parties by notice in writing. </p></li>
                                        <li><p>The validity and interpretation of this Agreement shall be governed exclusively by the laws of the Islamic Republic of Pakistan.</p></li>
                                        <li><p>Any and all claims, disputes, controversies or differences arising between the Partners out of or in relation to or in connection with this agreement, per the breach thereof, shall be determined by Arbitration in accordance with the commercial rules of the Arbitration Act 1940. The decision of an arbitrator or arbitrators, as the case may be, in such arbitration shall be final and binding upon the parties.</p></li>
                                        <li><p>This Agreement embodies the entire and final agreement of the Partners with regard to the arrangement and no representations, warranties, agreements, understandings, verbal or otherwise, exist between the Partners except as herein expressly set out.</p></li>
                                        <li><p>After the start of every fiscal year 10% increment will be applied on the base fare rates.</p></li>
                                    </ol>
                                    <p><strong>IN WITNESS WHEREOF</strong> the parties hereto have duly executed this Agreement this {{ date("l") }} day of {{date("d-m-Y")}}, to be effective as of the Original Effective Date.</p>
                    <form id="agreement-form" class="form form-horizontal" method="post" action="{{route('cod.update.agreement_status')}}">
                        @csrf
                        <div class="form-body">
                            <p><label class="checkbox-inline form-group text-left"><input type="checkbox" id="agreement_signed" value="1" required name="agreement_signed" data-msg-required="Please accept company's service terms and conditions."> &nbsp; &nbsp;I Agree</label></p>

                            <div class="mt-5 w-25">
                                <span class="form-group">
                                <img id="esign_image" src="" style="width: 100%;" alt="">
                                    <textarea data-rule-required="true" data-msg-required="Signature is Required" name="esign" id="esign" class="d-none"></textarea>
                                </span>
                                <hr style="border-top:1px solid black;">
                            </div>
                            <p><strong>Name :</strong> {{$shipper_name}}</p>

                        </div>
                        <div class="form-actions center">
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                            </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="SignatureModal" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="SignatureModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white text-center">
                    <h4 class="modal-title white">Draw Signature here or Upload E Signature Image</h4>

                </div>
                <div class="modal-body  text-center">

                    <div class="w-100">
                        <canvas id="e-sign-canvas">
                        </canvas>

                    </div>
                </div>
                <input type="file" class="d-none" id="upload_e_sign" accept=".png,.jpeg">
                <div class="modal-footer">
                    <button tabindex="-1" type="button" class="btn btn-primary ml-1" id="upload_img_btn" >Upload Image</button>
                    <button tabindex="-1" type="button" class="btn btn-success ml-1" id="save_signature_btn">Save E Sign</button>
                    <button type="button" class="btn btn-danger ml-1" id="clear_signature_btn">Clear</button>
                </div>
            </div>
        </div>
    </div>
@endif



@if(isset($visit) && $visit)

    <div class="modal fade" id="DailyVisitRateModal" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="DailyVisitRateModal"
         aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white text-center">
                    <h4 class="modal-title white">Please Rate Visit Of Our Salesperson</h4>
                </div>
                <form action="{{route('cod.rate_daily_visit')}}" method="post" id="DailyVisitRateForm">
                    @csrf
                    <div class="modal-body  text-center">
                        <div class="feedback">
                            @foreach(\App\Http\Models\Admin\DailyVisitRating::all() as $rating)
                                    <div class="item">
                                        <label for="{{ $rating->id }}" title="{{ $rating->name }}">
                                            <input class="radio" type="radio" name="rating" id="{{ $rating->id }}" value="{{ $rating->id }}">
                                            <span>{{$rating->code}}</span>
                                        </label>
                                    </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="daily_visit_id" value="{{$visit->id}}">
                        <input type="hidden" name="action" id="action_id" value="">
                        <textarea name="comment" class="form-control" id="comment" cols="30" rows="5" placeholder="Enter Comment"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button tabindex="-1" type="button" id="skip_daily_visit_btn" class="btn btn-primary ml-1">Skip</button>
                        <button tabindex="-1" type="button" id="rate_daily_visit_btn" class="btn btn-success ml-1">Rate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@if(Session::has('nps_survey') &&  !empty(session('nps_survey')))

    <div class="modal fade" id="question_modal" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="question_modal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white text-center">
                    <h4 class="modal-title white" id="question_survery_heading">Survey Form</h4>
                </div>
                <form action="{{route('cod.nps.ratting_submit')}}" method="post" id="question_submit">
                    @csrf
                    <div class="modal-body  text-center" style="width: 100%;overflow-y: scroll;height: 500px!important;">
                        <h5 style="text-align: left;">
                            <strong>Dear Valued Customer,</strong>
                            <br>
                            In order to improve our service and to assure you to be the part of improvement, we request you to submit your valuable feedback/rating against this survey. Please rate us on scale of <strong> 0-5</strong>.<br>
                            <strong> 0-2 (Bad).</strong>
                            <strong> 3 (Neutral) </strong>
                            <strong> 4-5 (Good) </strong>

                        </h5>
                        <table class="table table-bordered table-lg" id="question_data_table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th width="20">S.No </th>
                                    <th width="800">Questions</th>
                                    <th width="300">Rating Scale</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="col-12" id="question_recommend_input">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button tabindex="-1" type="button" id="skip_nps_survey" class="btn btn-primary ml-1">Skip</button>
                        <button tabindex="-1" type="button" id="rate_nps_survey" class="btn btn-success ml-1">Rate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endif