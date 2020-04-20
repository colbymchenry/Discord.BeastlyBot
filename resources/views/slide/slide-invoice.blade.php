<header class="slidePanel-header dual bg-grey-4">
    <div class="slidePanel-actions" aria-label="actions" role="group">
        <button type="button" class="btn btn-icon btn-pure btn-inverse slidePanel-close actions-top icon wb-close"
                aria-hidden="true"></button>
    </div>
    <h1>Invoice</h1>
    <p>@if($invoice->lines->data[0]->plan->metadata['stripe_express_id'] == auth()->user()->stripe_express_id && $invoice->metadata['refunded'] != 'true') @if($invoice->metadata['paid_out'] == 'true')<span class="badge badge-success">Paid Out</span>@else <span class="badge badge-primary">Pending</span> @endif @else <span class="badge badge-primary">@if($invoice->metadata['refunded'] == 'true')Refunded @else {{ $invoice->status }} @endif</span> @endif</p>
</header>

<div class="page-content">
    <div class="panel-heading">
        @if(Request::path() == '/server/{id}')
        <div class="panel-actions">
            <div class="dropdown">
                <a class="panel-action" data-toggle="dropdown" href="#" aria-expanded="false"><i
                        class="icon wb-settings" aria-hidden="true"></i></a>
                <div class="dropdown-menu dropdown-menu-bullet dropdown-menu-right" role="menu">
                    <a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-envelope"
                                                                                          aria-hidden="true"></i> Send
                        Reminder</a>
                    <a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-minus-circle"
                                                                                          aria-hidden="true"></i> Cancel Invoice</a>
                    <a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-heart"
                                                                                          aria-hidden="true"></i> Refund</a>
                    <!-- if first invoice and refund request available -->
                    <a class="dropdown-item" href="javascript:void(0)" role="menuitem"><i class="icon wb-heart"
                                                                                          aria-hidden="true"></i> Request Refund</a>

                </div>
            </div>
        </div>
        @endif

    </div>
    <!-- Panel -->
    <div class="panel border">
        <div class="panel-body container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <h4>Invoice <span class="font-size-20 font-weight-200">#{{ $invoice->number }}</span></h4>
                    <span>Date: <span class="font-weight-600">{{ gmdate("m-d-Y", $invoice->created) }}</span></span>
                    <br>
                    <span class="{{ $invoice->paid ? 'green-600' : 'red-600' }}">Paid: <span class="font-weight-600">{{ $invoice->paid ? 'Yes' : 'No' }}</span></span>
                    <br>
                    <span>Customer: <span class="font-weight-600 text-underline">{{ $invoice->customer_email }}</span></span>
                </div>
            </div>
            <br/>
            <div class="page-invoice-table table-responsive">
                <table class="table text-center">
                    <thead>
                    <tr>
                        {{--                        <th class="text-center">#</th>--}}
                        <th>Guild</th>
                        <th>Role</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td  id="guild_name">
                            {{ explode('_', $invoice->lines->data[0]['plan']->product)[0] }}
                        </td>
                        <td  id="role_name">
                            {{ explode('_', $invoice->lines->data[0]['plan']->product)[1] }}
                        </td>
                        <td>
                            ${{ number_format($invoice->amount_paid/100, 2, '.', ',') }}
                        </td>
                        <td>
                            ${{ number_format($invoice->amount_remaining/100, 2, '.', ',') }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="clearfix">
        <div class="float-right text-right">
            <p class="font-size-20 font-weight-200">Subtotal: <span>${{ number_format($invoice->subtotal/100, 2, '.', ',') }}</span></p>
            @if($invoice->discount !== null)
                <p><u>Promotion</u>
                    <br>
                    <span class="font-weight-600">Code: <span>{{ $invoice->discount->coupon->id }}</span></span>
                    <br>
                    @if($invoice->discount->coupon->amount_off !== null)
                        Affect: <span class="grey-200">-${{ number_format($invoice->discount->coupon->amount_off/100, 2, '.', ',') }}</span>
                    @elseif($invoice->discount->coupon->percent_off !== null)
                        Affect: <span class="grey-200">-{{ $invoice->discount->coupon->percent_off }}%</span>
                    @endif
                    <br>
                    Duration: <span class="text-capitalize">{{ $invoice->discount->coupon->duration }}</span>
                </p>
            @endif
            <p class="page-invoice-amount font-size-20 green-600 font-weight-400">Total:
                <span>${{ number_format($invoice->total/100, 2, '.', ',') }}</span>
            </p>
            @if ($invoice->metadata['refunded'] == 'true')
            <p class="font-size-20 blue-600 font-weight-200">Refunded</span></p>
            @endif
        </div>
    </div>
    <div class="text-right">
        <button type="button" class="btn btn-dark btn-outline" onclick="window.open('{{ $invoice->invoice_pdf }}','_blank')">
            <i class="icon wb-print" aria-hidden="true"></i> Print
        </button>
    </div>
    <!-- End Panel -->
</div>

<script type="text/javascript">
    var guild_id = $('#guild_name').text().replace(/\s/g, '');
    var role_id = $('#role_name').text().replace(/\s/g, '');
    $(document).ready(function () {
        socket.emit('get_guild_data', [socket_id, guild_id]);
        socket.emit('get_role_data', [socket_id, guild_id, role_id]);

        socket.on('res_guild_data_' + socket_id, function (message) {
            $('#guild_name').text(message['name']);
        });

        socket.on('res_role_data_' + socket_id, function (message) {
            $('#role_name').text(message['name']);
        });
    });
</script>

@include('partials/clear_script')
