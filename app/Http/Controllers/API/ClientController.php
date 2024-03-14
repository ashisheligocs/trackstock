<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\InvoiceReturn;
use App\Models\InvoicePayment;
use App\Models\NonInvoicePayment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\InvoiceResource;
use App\Http\Requests\ClientStoreRequest;
use Illuminate\Support\Facades\Validator;
use Spatie\SimpleExcel\SimpleExcelReader;
use App\Http\Resources\ClientListResource;
use App\Notifications\WelcomeNotification;
use App\Http\Resources\InvoiceListResource;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\InvoicePaymentResource;
use Intervention\Image\Facades\Image as Image;
use App\Http\Resources\InvoiceForPaymentResource;
use App\Http\Resources\InvoiceReturnListResource;
use App\Http\Resources\NonInvoicePaymentListResource;
use App\Http\Resources\ClientWithInvoicePaymentResource;
use App\Http\Resources\ClientWithNonInvoicePaymentResource;
use App\Http\Resources\AccountTransactionResource;
use App\Models\AccountTransaction;
use Modules\Accounts\Entities\LedgerAccount;
use Modules\Accounts\Entities\PlutusEntries;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
class ClientController extends Controller
{
    // define middleware
    public function __construct()
    {
        $this->middleware('can:client-list', ['only' => ['index', 'search']]);
        $this->middleware('can:client-create', ['only' => ['create']]);
        $this->middleware('can:client-view', ['only' => ['show']]);
        $this->middleware('can:client-edit', ['only' => ['update']]);
        $this->middleware('can:client-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *                                                                                                                                                                                                          YYYYY
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ClientListResource::collection(Client::orderBy('id', 'DESC')->paginate($request->perPage));
    }


     // getting agent details
     public function getting_client_detail(Request $request){
        // dd("DSfsdfsdf");
        $client_data = Client::where('slug', $request->name)->get();

        return response()->json([
            'status' => true,
            'data' => $client_data,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientStoreRequest $request)
    {
       
        try {
            
            // generate code
            $code = 1;
            $lastClient = Client::latest()->first();
            if ($lastClient) {
                $code = $lastClient->client_id + 1;
            }
            $clientsDirectory = public_path('images/clients');
            if (!File::exists($clientsDirectory)) {
                File::makeDirectory($clientsDirectory, 0755, true);
            }
            // upload thumbnail and set the name
            $imageName = null;
            if ($request->images && !empty($request->images)) {
                $imageName = [];
                foreach ($request->images as $image) {
                    if ($image) {
                        $name = time().$this->generateRandomString(12).'.'.$image->getClientOriginalExtension();
                        Image::make($image)->save(public_path('images/clients/') . $name);
                        $imageName[] = $name;
                    }
                }
            }
            if(!empty($request->file)){
                $fileDetails = $_FILES['file'];
                if ($fileDetails && !empty($fileDetails)) {
                    /*Handle Multiple File Upload*/

                    for ($i = 0; $i < count($fileDetails['name']); $i++) {
                            
                            $name    = $fileDetails['name'][$i];
                            $tmpName = $fileDetails['tmp_name'][$i];
                            $type    = $fileDetails['type'][$i];
                            $error   = $fileDetails['error'][$i];
                            $size    = $fileDetails['size'][$i];

                            if ($error === 0) {
                                $name = time() . $this->generateRandomString(12) . '.jpeg';
                                $destination = public_path('images/clients/') . $name;
                                move_uploaded_file($tmpName, $destination);
                                $imageName[] = $name;
                            }
                        }
                        // Check if there were no errors during the upload
                }
            }
            // create client
            $userSchema = Client::create([
                'name' => $request->name,
                'client_id' => $code,
                'email' => $request->email,
                'phone' => $request->phoneNumber,
                'gst' => $request->gst ?? 1,
                'company_name' => $request->companyName,
                'type' => @$request->type ?? 1,
                'address' => $request->address,
                'status' => $request->status,
                'image_path' => $imageName && !empty($imageName) ? $imageName[0] : '',
                'images' => $imageName,
                'identity' => $request->identity,
                'nationality' => $request->nationality,
                'hotel_id' => @$request->hotel_id['id']
            ]);
            // dd($request->gst);
            
            if(!empty($request->type) && $request->type == 2){
                // get role
                $role = Role::where('slug', 'agent')->first();
                // store user
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'account_role' => 0,
                ]);

                $userSchema->user_id = $user->id;
                $userSchema->save();
                
                $user->roles()->attach($role->id);
                $user->permissions()->attach($user->roles[0]->permissions);
                // !empty($request->hotel_id) && $user->hotels()->sync($request->hotel_id);
            }

            //send welcome notification
            try {
                if ($request->isSendEmail === 'true' || $request->isSendSMS === 'true') {
                    Notification::send($userSchema, new WelcomeNotification($userSchema, [
                        'isSendEmail' => $request->isSendEmail,
                        'isSendSMS' => $request->isSendSMS,
                    ]));
                }
            } catch (Exception $e) {
                //handle email error here if necessary
                throw new Exception($e);
            }

            return $this->responseWithSuccess('Client added successfully', $userSchema);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        try {
            $client = Client::where('slug', $slug)->first();

            return new ClientResource($client);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $client = Client::where('slug', $slug)->first();


        // validate request
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20|min:10',
            // 'email' => 'nullable|email|max:255|min:3|unique:clients,email,' . $client->id,
            // 'email' => 'nullable|email|max:255|min:3,email,' . $client->id,
            'companyName' => 'nullable|string|max:100|min:2',
            'address' => 'nullable|string|max:255',
            
//            'hotel_id' => 'required'
        ]);
        try {

            // upload thumbnail and set the name
            $imageName = null;
            if ($request->images && !empty($request->images)) {
                $imageName = [];
                foreach ($request->images as $image) {
                    if ($image) {
                        $name = time().$this->generateRandomString(12).'.'.$image->getClientOriginalExtension();
                        Image::make($image)->save(public_path('images/clients/') . $name);
                        $imageName[] = $name;
                    }
                }
            }

            if ($request->pastImages && !empty($request->pastImages)) {
                $imageName = array_merge($imageName ?? [], $request->pastImages);
            }

            if(!empty($request->file)){
                // dd($request->file);
                $fileDetails = $_FILES['file'];
                if ($fileDetails && !empty($fileDetails)) {
                    
                    /*Handle Multiple Files for upload*/

                    for ($i = 0; $i < count($fileDetails['name']); $i++) {
                        
                        $name    = $fileDetails['name'][$i];
                        $tmpName = $fileDetails['tmp_name'][$i];
                        $type    = $fileDetails['type'][$i];
                        $error   = $fileDetails['error'][$i];
                        $size    = $fileDetails['size'][$i];

                        // Check if there were no errors during the upload
                        if ($error === 0) {
                            $name = time() . $this->generateRandomString(12) . '.jpeg';
                            $destination = public_path('images/clients/') . $name;
                            move_uploaded_file($tmpName, $destination);
                            $imageName[] = $name;
                        }
                    }
                }
            }

            // update client
            $client->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phoneNumber,
                'gst' => $request->gst,
                'company_name' => $request->companyName,
                'address' => $request->address,
                'status' => $request->status,
                'type' => $request->type ?? 1,
                'image_path' => $imageName && !empty($imageName) ? $imageName[0] : null,
                'images' => $imageName,
                'identity' => $request->identity,
                'nationality' => $request->nationality,
                'hotel_id' => @$request->hotel_id['id'] ?? 1
            ]);
            $client->refresh();


            return $this->responseWithSuccess('Client updated successfully', $client);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        try {
            $client = Client::where('slug', $slug)->first();

            $canDelete = true;
            if (count($client->clientInvoices) > 0 || count($client->clientNonInvoiceDues) > 0) {
                $canDelete = false;

                return $this->responseWithError('Sorry you can\'t delete this client!');
            }
            if ($canDelete) {
                //delete asset image
                if ($client->image_path) {
                    @unlink(public_path('images/clients/' . $client->image_path));
                }
                $client->delete();
            }

            return $this->responseWithSuccess('Asset deleted successfully');
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * search resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(Request $request)
    {
        $term = $request->term;
        $query = Client::query();

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        }
        if (@$request->customer_only) {
            $query = $query->where('type', 1)->where('slug', '!=', 'walking-customer');
        }

        $query->where(function ($query) use ($term) {
            $query->where('name', 'Like', '%' . $term . '%')
                ->orWhere('client_id', 'Like', '%' . $term . '%')
                ->orWhere('email', 'Like', '%' . $term . '%')
                ->orWhere('phone', 'Like', '%' . $term . '%')
                ->orWhere('company_name', 'Like', '%' . $term . '%');
        });

        return ClientResource::collection($query->latest()->paginate($request->perPage));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allClients(Request $request)
    {

        if(@$request->input('hotel_id')){
            $clients = Client::where('status', 1)->where('type', 1)->where('hotel_id',$request->input('hotel_id'))->latest()->get();
        } else {
            $clients = Client::where('status', 1)->where('type', 1)->latest()->get();
        }

        return ClientListResource::collection($clients);
    }

    public function allAgentsClient(Request $request)
    {

        if(@$request->input('hotel_id')){
            $clients = Client::where('status', 1)->where('hotel_id',$request->input('hotel_id'))->latest()->get();
        } else {
            $clients = Client::where('status', 1)->latest()->get();
        }

        return ClientListResource::collection($clients);
    }

    // return all clients for non invoice payments
    public function clientsForNonInvoicePayments()
    {
        $clients = Client::where('status', 1)->latest()->get();

        return ClientWithNonInvoicePaymentResource::collection($clients);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function clientInvoices($slug)
    {
        try {
            $client = Client::where('slug', $slug)->with('clientInvoices')->first();
            return InvoiceResource::collection($client->clientInvoices);
        } catch (Exception $e) {
            return $this->responseWithError($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filterClientInvoices(Request $request)
    {
        $client = Client::where('slug', $request->clientSlug)->first();
        $products = [];
        $invoices = Invoice::with('client', 'invoiceProducts')->whereDoesntHave('invoiceReturn')->where(
            'client_id',
            $client->id
        );
        if (isset($request->products) && count($request->products) > 0) {
            // build the product array
            foreach ($request->products as $key => $product) {
                array_push($products, $product['id']);
            }
            // get the invoices
            $invoices = $invoices->whereHas('invoiceProducts', function ($firstQuery) use ($products) {
                $firstQuery->whereIn('product_id', $products);
            })->get();
        } else {
            // get the invoices
            $invoices = $invoices->get();
        }

        return InvoiceResource::collection($invoices);
    }

    // return client specific invoices
    public function specificClientInvoices($slug)
    {
        $client = Client::where('slug', $slug)->first();
        $invoices = Invoice::with(
            'client',
            'invoicePayments',
            'invoiceReturn',
            'invoiceTax',
            'invoiceProducts'
        )->where('client_id', $client->id)->get();

        return [
            'invoices' => InvoiceForPaymentResource::collection($invoices->where('calculated_due', '>', 0)),
            'client' => new ClientWithInvoicePaymentResource($client),
        ];
    }

    // return client all invoices
    public function clientAllInvoices(Request $request, $slug)
    {

        $client = Client::where('slug', $slug)->first();
       
        return InvoiceListResource::collection(Invoice::with(
            'client',
            'invoiceTax',
            'invoicePayments'
        )->where('client_id', $client->id)->latest()->paginate($request->perPage));
    }

    // search client invoices
    public function searchClientInvoices(Request $request, $slug)
    {
        $term = $request->term;

        $client = Client::where('slug', $slug)->first();

        $query = Invoice::with('client', 'invoiceTax', 'invoicePayments')->where('client_id', $client->id);

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('invoice_date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where(function ($query) use ($term) {
                $query->where('invoice_no', 'LIKE', '%' . $term . '%')
                    ->orWhere('sub_total', 'LIKE', '%' . $term . '%')
                    ->orWhere('po_reference', 'LIKE', '%' . $term . '%')
                    ->orWhere('payment_terms', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('client', function ($newQuery) use ($term) {
                        $newQuery->where('name', 'LIKE', '%' . $term . '%')
                            ->orWhere('client_id', 'LIKE', '%' . $term . '%');
                    });
            });
        });

        return InvoiceListResource::collection($query->latest()->paginate($request->perPage));
    }

    // return client invoice returns
    public function clientInvoiceReturns(Request $request, $slug)
    {
        $client = Client::where('slug', $slug)->first();
        $invoices = InvoiceReturn::with('invoice.client', 'user')->whereHas(
            'invoice',
            function ($newQuery) use ($client) {
                $newQuery->where('client_id', $client->id);
            }
        );

        return InvoiceReturnListResource::collection($invoices->latest()->paginate($request->perPage));
    }

    // search client invoice returns
    public function searchClientInvoiceReturns(Request $request, $slug)
    {
        $term = $request->term;

        $client = Client::where('slug', $slug)->first();

        $query = InvoiceReturn::with('invoice.client', 'user');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term, $client) {
            $query->whereHas('invoice', function ($newQuery) use ($client) {
                $newQuery->where('client_id', $client->id);
            })->where(function ($query) use ($term) {
                $query->where('reason', 'LIKE', '%' . $term . '%')
                    ->orWhere('slug', 'LIKE', '%' . $term . '%')
                    ->orWhere('total_return', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('invoice', function ($newQuery) use ($term) {
                        $newQuery->where('invoice_no', 'LIKE', '%' . $term . '%')
                            ->orWhere('po_reference', 'LIKE', '%' . $term . '%')
                            ->orWhereHas('client', function ($anotherQuery) use ($term) {
                                $anotherQuery->where('name', 'LIKE', '%' . $term . '%');
                            });
                    });
            });
        });

        return InvoiceReturnListResource::collection($query->latest()->paginate($request->perPage));
    }

    // return client invoice payments
    public function clientInvoicePayments(Request $request, $slug)
    {
        $client = Client::where('slug', $slug)->first();

        // DB::connection()->enableQueryLog();


        $payments = InvoicePayment::with(
            'invoice.client',
            'invoice.invoiceTax',
            'invoicePaymentTransaction.cashbookAccount',
            'user'
        )->whereHas(
            'invoice',
            function ($newQuery) use ($client) {
                // $newQuery->whereHas('client', function ($anotherQuery) use ($client) {
                    $newQuery->where('client_id', $client->id);
                // });
            }
        )->latest()->paginate($request->perPage);

        // dd(DB::getQueryLog());


        return InvoicePaymentResource::collection($payments);
    }

    // search client invoice payments
    public function searchClientInvoicePayments(Request $request, $slug)
    {
        $term = $request->term;

        $client = Client::where('slug', $slug)->first();

        $query = InvoicePayment::with(
            'invoice.client',
            'invoice.invoiceTax',
            'invoicePaymentTransaction.cashbookAccount',
            'user'
        );

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term, $client) {
            $query->whereHas('invoice', function ($newQuery) use ($client) {
                // $newQuery->whereHas('client', function ($anotherQuery) use ($client) {
                    $newQuery->where('client_id', $client->id);
                // });
            })->where(function ($query) use ($term) {
                $query->where('amount', '=', $term)
                    ->orWhereHas('invoice', function ($newQuery) use ($term) {
                        $newQuery->where('invoice_no', 'LIKE', '%' . $term . '%')
                            ->orWhere('po_reference', 'LIKE', '%' . $term . '%')
                            ->orWhereHas('client', function ($anotherQuery) use ($term) {
                                $anotherQuery->where('name', 'LIKE', '%' . $term . '%')
                                    ->orWhere('phone', 'LIKE', '%' . $term . '%');
                            });
                    })
                    ->orWhereHas('invoicePaymentTransaction', function ($newQuery) use ($term) {
                        $newQuery->where('cheque_no', 'LIKE', '%' . $term . '%')
                            ->orWhere('receipt_no', 'LIKE', '%' . $term . '%')
                            ->orWhereHas('cashbookAccount', function ($newQuery) use ($term) {
                                $newQuery->where('account_number', 'LIKE', '%' . $term . '%')
                                    ->orWhere('bank_name', 'LIKE', '%' . $term . '%');
                            });
                    });
            });
        });
        return InvoicePaymentResource::collection($query->latest()->paginate($request->perPage));
    }

    // return client non invoice payments
    public function clientNonInvoicePayments(Request $request, $slug)
    {
        $client = Client::where('slug', $slug)->first();
        $transactions = NonInvoicePayment::with('client', 'paymentTransaction.cashbookAccount')->where(
            'client_id',
            $client->id
        )->latest()->paginate($request->perPage);

        return NonInvoicePaymentListResource::collection($transactions);
    }

    // search non invoice payments
    public function searchClientNonInvoicePayments(Request $request, $slug)
    {
        $term = $request->term;

        $client = Client::where('slug', $slug)->first();

        $query = NonInvoicePayment::with('client', 'paymentTransaction.cashbookAccount');

        if ($request->startDate && $request->endDate) {
            $query = $query->whereBetween('date', [$request->startDate, $request->endDate]);
        }

        $query->where(function ($query) use ($term, $client) {
            $query->where('client_id', $client->id)->where(function ($query) use ($term) {
                $query->where('amount', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('paymentTransaction', function ($newQuery) use ($term) {
                        $newQuery->where('cheque_no', 'LIKE', '%' . $term . '%')
                            ->orWhere('receipt_no', 'LIKE', '%' . $term . '%')->orWhereHas(
                                'cashbookAccount',
                                function ($newQuery) use ($term) {
                                    $newQuery->where('account_number', 'LIKE', '%' . $term . '%')
                                        ->orWhere('bank_name', 'LIKE', '%' . $term . '%');
                                }
                            );
                    });
            });
        });

        // return $query->toSql();

        return NonInvoicePaymentListResource::collection($query->latest()->paginate($request->perPage));
    }

    // csv import
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:csv', 'file'],
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data = SimpleExcelReader::create($file, 'csv')->getRows();

            $rules = [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20|min:3',
                'email' => 'nullable|email|max:255|min:3|unique:clients,email',
                'company_name' => 'nullable|string|max:100|min:2',
                'address' => 'nullable|string|max:255',
            ];

            foreach ($data as $key => $item) {
                $validator = Validator::make($item, $rules);
                if ($validator->passes()) {
                    Client::create(
                        $this->incrementClientId() +
                            $validator->validated()
                    );
                } else {
                    return response()->json([
                        'message' => $validator->errors()->first(),
                        'row_number' => $key + 1
                    ], 422);
                }
            }
            return response()->json([
                'message' => 'Clients imported successfully'
            ]);
        }
    }

    public function incrementClientId()
    {
        $clientId = 1;
        $lastClient = Client::latest('id')->first();
        if ($lastClient) {
            $clientId = (int) $lastClient->client_id + 1;
        }
        return [
            'client_id' => $clientId
        ];
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // client
    public function specificClientLedger(Request $request,$slug)
    {

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $client = Client::where('slug', $slug)->first();

        $commissionPayable = LedgerAccount::where('code','commission-payable')->first();
        $chargeAccount = LedgerAccount::where('code','CHARGES')->first();
        $accountReceieve = LedgerAccount::where('code','ACCOUNT-RECEIVABLE')->first();
        // $directSale = LedgerAccount::where('code','DIRECT-SALES')->first();
        // $data = AccountTransaction::with('cashbookAccount')
        //     ->where('customer_id', $client->id)->whereIn('account_id',['15','23'])->latest()->get();


        // $data = AccountTransaction::with('booking.invoice')->where('customer_id', $client->id)->whereHas('cashbookAccount', function ($query) {
        //     $query->whereHas('ledgerAccount', function ($q) {
        //         $q->where('is_bank', 1)
        //             ->orWhereIN('id', [23]);
        //     });
        // })->get();

        // dd($data);
            // $finalData =  AccountTransactionResource::collection($data);

        // $totalDiscount = 0
        // $totalDebit = $totalCredit = $finalBalance = 0;
        // $balance = 0;


        // foreach ($data as &$ledger) {

        //     if ($ledger->type == 0) {
        //         // var_dump($ledger->is);
        //         if($ledger->asset || $ledger->expense){
        //             $ledger->balance = $balance + $ledger->amount;
        //             $balance = $ledger->balance;
        //             $totalDebit  += $ledger->amount;
        //         } else {
        //             $balance =  $ledger->balance = $balance - $ledger->amount;
        //             $totalCredit  += $ledger->amount;
        //         }
        //     } else {
        //         if($ledger->asset || $ledger->expense){
        //             $balance =  $ledger->balance = $balance - $ledger->amount;
        //             $totalCredit  += $ledger->amount;

        //         } else {
        //             $ledger->balance = $balance + $ledger->amount;
        //             $balance = $ledger->balance;
        //             $totalDebit  += $ledger->amount;
        //         }
        //     }
        //     // $totalDiscount += $ledger->booking->discount_amount;
        // }
        // $finalBalance = $totalDebit - $totalCredit;

        // return [
        //     'item' => AccountTransactionResource::collection($data),
        //     'totalDebit' => $totalDebit,
        //     'totalCredit' => $totalCredit,
        //     'finalBalance' => $finalBalance,
        //     // 'totalDiscount' => $totalDiscount
        // ];
        // return AccountTransactionResource::collection($data);

        $additional_query = '';
            if(!empty($chargeAccount) && !empty($commissionPayable)){
                $additional_query = 'OR la.id IN ('.$chargeAccount->id.','.$commissionPayable->id.','.$accountReceieve->id.') AND (atr.type !=0 AND la.id = atr.account_id)';
            }
        $data = DB::select("SELECT date
        ,particulars
            ,slug
            ,action_type
            ,debit
            ,discount
            ,credit
            ,original_date
        FROM (
            SELECT ii.created_at date
                ,CONCAT ('Invoice [ATI-',ii.invoice_no, ']')  particulars
                ,ii.slug
                ,'invoice' as action_type
                ,IFNULL(sub_total, 0) + IFNULL(tax_value,0) - IFNULL(discount, 0) + IFNULL((SELECT sum(ci.total_return) FROM invoice_returns ci WHERE ii.id = ci.invoice_id),0) + IFNULL((SELECT sum(ci.total_return_tax) FROM invoice_returns ci WHERE ii.id = ci.invoice_id),0) debit
                ,b.discount_amount + IFNULL(b.special_discount_amount,0) discount
                ,0 credit
                ,ii.invoice_date as original_date
                ,ii.client_id

            FROM `invoices` ii,bookings b
            WHERE b.invoice_id = ii.id

            UNION ALL

            SELECT ci.created_at date
                ,CONCAT (
                    'Invoice Return [ATI-'
                    ,`invoice_id`
                    , ']') particulars
                ,ci.slug
                ,'invoice-return' as action_type
                ,0 debit
                ,0 discount
                ,IFNULL(ci.total_return, 0) + IFNULL(ci.total_return_tax, 0) credit
                ,ci.date as original_date
                ,ii.client_id
            FROM `invoices` ii
                ,invoice_returns ci
            WHERE ii.id = ci.invoice_id

            UNION ALL

            SELECT atr.created_at date
                ,atr.reason as particulars
                ,atr.slug
                ,'invoice-advance-payment' as action_type
                ,IF(atr.type='0' || atr.account_id=$chargeAccount->id || atr.account_id=$accountReceieve->id,atr.amount,0) debit
                ,0 discount
                ,IF(atr.type='1' && atr.account_id!=$chargeAccount->id && atr.account_id!=$accountReceieve->id, atr.amount, 0) + atr.bank_charges credit
                ,atr.transaction_date as original_date
                ,atr.customer_id as client_id

                FROM `account_transactions` atr,ledgers_accounts la
                WHERE la.id = atr.account_id AND (la.is_bank = 1 ".$additional_query." )
            ) v
        WHERE v.client_id = " . $client->id . " AND `date` BETWEEN '$startDate' AND '$endDate'
        ORDER BY `date` ASC");

        // AND booking_id IS NULL
        // AND (atr.type !=0 AND la.id = atr.account_id)
        //if credit plus, debit minus balance, always discount minus
        // dd($data);
        $totalDiscount = $totalDebit = $totalCredit = $finalBalance = 0;
        $balance = 0;
        foreach ($data as &$ledger) {
            if ($ledger->credit == 0) {
                $ledger->balance = $balance + $ledger->debit;
                $balance = $ledger->balance;
            } else {
                $balance =  $ledger->balance = $balance - $ledger->credit - $ledger->discount;
            }
            $totalDiscount  += $ledger->discount;
            $totalDebit  += $ledger->debit;
            $totalCredit  += $ledger->credit;
        }

        $finalBalance = $totalCredit - $totalDebit;
        return [
            'items' => $data,
            'totalDiscount' => $totalDiscount,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'finalBalance' => $finalBalance,
        ];
    }

    /*Adjust Agent,Agency Or Guide Commission Payment*/
    public function clientPaymentPay(Request $request){

        DB::beginTransaction();
        try {

            $getCustomer = Client::where('id',$request->customerId)->first();
            $currentDateTime = date('Y-m-d H:i:s');
            if($request->input('commission')){
                $note = 'Commission Payment Paid to '.$getCustomer->name;
            } else {
                $note = 'Booking Refund Payment Paid to '.$getCustomer->name;
            }
            /*Setup entry for plutus entry*/
            $createPlutus = PlutusEntries::create([
                'hotel_id' => $request->hotelId,
                'note' => $note,
                'date' => $request->paymentDate,
                'amount' => $request->paidAmount,
            ]);

            $query = LedgerAccount::find($request->account['id']);
            $account = $query->getAccoutnbalance;

            $accounts = $account->balanceTransactions()->create([
                'amount' => round($request->paidAmount, 2),
                'reason' => ($request->note != '') ? $request->note .' from '.$request->account['ledgerName'] : "Commission Payment Paid from ".$request->account['ledgerName'],
                'type' => 0,
                'transaction_date' => $request->paymentDate. ' ' .date('H:i:s', strtotime($currentDateTime)),
                'cheque_no' => null,
                'receipt_no' => null,
                'created_by' => auth()->user()->id,
                'status' => 1,
                'note' => ($request->note != '') ? $request->note : 'Commission Payment',
                'booking_id' => null,
                'hotel_id' => $request->hotelId,
                // 'customer_id'=> ($request->input('commission')) ? null : $request->customerId,
                'customer_id'=> $request->customerId,
                'plutus_entries_id' => $createPlutus->id,
                'bank_charges' => 0
            ]);

            if($request->input('commission')){
                $commissionAccount = LedgerAccount::where('code', 'commission-payable')->first();
                $commissionAccount = $commissionAccount->getAccoutnbalance;

                if($commissionAccount) {
                    $commission[] = $commissionAccount->balanceTransactions()->create([
                        'amount' => round($request->paidAmount, 2),
                        'reason' => ($request->note != '') ? $request->note .' from '.$request->account['ledgerName'] : "Commission Payment Paid from ".$request->account['ledgerName'],
                        'type' => 0,
                        'transaction_date' => $request->paymentDate. ' ' .date('H:i:s', strtotime($currentDateTime)),
                        'cheque_no' => null,
                        'receipt_no' => null,
                        'created_by' => auth()->user()->id,
                        'status' => 1,
                        'note' => ($request->note != '') ? $request->note : 'Commission Payment',
                        'booking_id' => null,
                        'hotel_id' => $request->hotelId,
                        'customer_id'=> $request->customerId,
                        'plutus_entries_id' => $createPlutus->id,
                        'bank_charges' => 0
                    ]);
                }
            } else {
                $refundAmountFromAccount = LedgerAccount::where('code', 'ACCOUNT-PAYABLE')->first();
                $refundAmountFromAccount = $refundAmountFromAccount->getAccoutnbalance;
                if ($refundAmountFromAccount) {
                    $refundAmountFromAccount->balanceTransactions()->create([
                        'amount'           => round($request->paidAmount, 2),
                        'reason'           => ($request->note != '') ? $request->note .' from '.$request->account['ledgerName'] : "Commission Payment Paid from ".$request->account['ledgerName'],
                        'type'             => 0,
                        'transaction_date' => $request->paymentDate. ' ' .date('H:i:s', strtotime($currentDateTime)),
                        'cheque_no'        => null,
                        'receipt_no'       => null,
                        'created_by'       => auth()->user()->id,
                        'status'           => 1,
                        'booking_id'       => null,
                        'hotel_id'         => $request->hotelId,
                        'customer_id'      => $request->customerId,
                        'plutus_entries_id'   => $createPlutus->id,
                        'bank_charges' => 0
                    ]);
                }
            }

            DB::commit();
            return $this->responseWithSuccess('Payment added successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseWithError($e->getMessage());
        }
    }

    /*Manage Advance Payment*/

    public function clientPaymentAdd(Request $request){
        DB::beginTransaction();
        try {

            $getCustomer = Client::where('id',$request->customerId)->first();
            $currentDateTime = date('Y-m-d H:i:s');

            $note = 'Advance Payment received from '.$getCustomer->name;
            /*Setup entry for plutus entry*/
            $createPlutus = PlutusEntries::create([
                'hotel_id' => $request->hotelId,
                'note' => $note,
                'date' => $request->paymentDate,
                'amount' => $request->paidAmount,
            ]);

            $query = LedgerAccount::find($request->account['id']);
            $account = $query->getAccoutnbalance;
            if($account){
                $accounts = $account->balanceTransactions()->create([
                    'amount' => round($request->paidAmount, 2),
                    'reason' => ($request->note != '') ? $request->note .' to '.$request->account['ledgerName'] : $note,
                    'type' => 1,
                    'transaction_date' => $request->paymentDate. ' ' .date('H:i:s', strtotime($currentDateTime)),
                    'cheque_no' => null,
                    'receipt_no' => null,
                    'created_by' => auth()->user()->id,
                    'status' => 1,
                    'note' => ($request->note != '') ? $request->note : 'Commission Payment',
                    'booking_id' => null,
                    'hotel_id' => $request->hotelId,
                    'customer_id'=> $request->customerId,
                    'plutus_entries_id' => $createPlutus->id,
                    'bank_charges' => 0
                ]);
            }


            $amountFromAccount = LedgerAccount::where('code', 'ACCOUNT-PAYABLE')->first();
            $amountFromAccount = $amountFromAccount->getAccoutnbalance;
            if ($amountFromAccount) {
                $amountFromAccount->balanceTransactions()->create([
                    'amount'           => round($request->paidAmount, 2),
                    'reason'           => ($request->note != '') ? $request->note .' to '.$request->account['ledgerName'] : $note,
                    'type'             => 1,
                    'transaction_date' => $request->paymentDate. ' ' .date('H:i:s', strtotime($currentDateTime)),
                    'cheque_no'        => null,
                    'receipt_no'       => null,
                    'created_by'       => auth()->user()->id,
                    'status'           => 1,
                    'booking_id'       => null,
                    'hotel_id'         => $request->hotelId,
                    'customer_id'      => $request->customerId,
                    'plutus_entries_id'   => $createPlutus->id,
                    'bank_charges' => 0
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseWithError($e->getMessage());
        }
    }







}
