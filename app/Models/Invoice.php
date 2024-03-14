<?php

namespace App\Models;

use App\Models\Scopes\selectedHotelResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Hotel\Entities\Booking;
use Modules\Hotel\Entities\BookingDetails;
use Modules\Hotel\Entities\Hotel;
use Modules\Restaurant\Entities\Restroorder;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_no', 'slug', 'reference', 'client_id', 'discount_type', 'hotel_id', 'discount', 'transport', 'sub_total', 'po_reference', 'payment_terms', 'delivery_place', 'tax_id', 'invoice_date', 'created_by', 'note', 'status', 'is_paid', 'tax_value'
    ];

    protected $appends = ['calculated_due', 'calculated_tax', 'calculated_total','calculated_service_tax'];

    /**
     * Get the invoice due.
     *
     * @return string
     */
    public function getCalculatedDueAttribute()
    {
        return $this->totalDue();
    }

    /**
     * Get the invoice tax.
     *
     * @return string
     */
    public function getCalculatedTaxAttribute()
    {
        return $this->taxAmount();
    }

    /**
     * Get the invoice total.
     *
     * @return string
     */
    public function getCalculatedTotalAttribute()
    {
        return $this->invoiceTotal();
    }

    public function getCalculatedServiceTaxAttribute()
    {
        return $this->invoiceServiceTotal();
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function CompanyDetails()
    {
        return $this->belongsTo(GeneralSetting::class);
    }

    // calculate tax
    public function taxAmount()
{
    // Remove or move the return statement to the end
    // dd($this->tax_value);

    if (!empty($this->tax_value)) {
        return $this->tax_value;
    }

    if (isset($this->invoiceServiceTax)) {
        $totalPaid = 0;
        foreach ($this->invoiceServiceTax as $tax) {
            $totalPaid += $tax->amount;
        }
        return $totalPaid;
    }

    $taxRate = $this->invoiceTax;
    $totalTax = 0;
    $subTotal = $this->sub_total;
    if (isset($taxRate) && $taxRate->rate > 0) {
        if (isset($this->invoiceReturn)) {
            $subTotal = $this->sub_total - $this->invoiceReturn->total_return;
        }
        $totalTax = ($taxRate->rate / 100) * $subTotal;
    }

    // Move or remove the return statement here
    return $totalTax;
}




    public function booking()
    {
        return $this->hasOne(Booking::class);
    }

    public function restaurantOrder()
    {
        return $this->hasOne(Restroorder::class);
    }

    public function bookingDetails()
    {
        return $this->hasManyThrough(BookingDetails::class, Booking::class);
    }

    // return discount percentage
    public function discountPercentage()
    {
        $percentage = null;
        if ($this->discount_type == 1) {
            $costOfReturn = isset($this->invoiceReturn) ? $this->invoiceReturn->total_return : 0;
            $percentage = ($this->discount * 100) / ($this->sub_total - $costOfReturn);
        }

        return (int) $percentage;
    }

    // invoice total
    public function invoiceTotal()
    {
        $costOfProductReturn = isset($this->invoiceReturn) ? $this->invoiceReturn->total_return : 0;
        return $this->sub_total + $this->transport + +$this->taxAmount() - $this->discount - $costOfProductReturn;
    }

    // purchase total paid
    public function invoiceTotalPaid()
    {
        $totalPaid = 0;
        // payments
        $invoicePayments = $this->invoicePayments;
        // total paid
        if (isset($invoicePayments)) {
            $totalPaid = $invoicePayments->sum('amount');
        }

        return $totalPaid;
    }

    public function invoiceServiceTotal()
    {
        $totalPaid = 0;
        // payments
        $invoiceServiceTax = $this->invoiceServiceTax;
        // total paid
        if (isset($invoiceServiceTax)) {
            foreach($invoiceServiceTax as $tax){
                $totalPaid += $tax->amount;
            }
        }

        return $totalPaid;
    }

    public function bookingDiscount()
    {
        return $this->booking ? ($this->booking->special_discount_amount + $this->booking->discount_amount) : 0;
    }

    // invoice total due
    public function totalDue()
    {
        $due = $this->invoiceTotal() - $this->invoiceTotalPaid();
        return $due >= 0 ? $due : 0;
    }

    /**
     * Get the invoice products.
     */
    public function invoiceProducts()
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_id', 'id')->orderBy('product_id');
    }

    public function invoiceService()
    {
        return $this->hasMany(InvoiceService::class, 'invoice_id');
    }

    public function invoiceServiceTax()
    {
        return $this->hasMany(InvoiceServiceTax::class, 'invoice_id');
    }

    /**
     * Get the invoice payments.
     */
    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }

    /**
     * Get the invoice returns.
     */
    public function invoiceReturn()
    {
        return $this->hasOne(InvoiceReturn::class, 'invoice_id');
    }

    /**
     * Get the invoice return products.
     */
    public function invoiceReturnProducts()
    {
        return $this->hasManyThrough(InvoiceReturnProduct::class, InvoiceReturn::class, 'invoice_id', 'return_id')->orderBy('product_id');
    }

    /**
     * Get the client for this quotation.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get the tax for this invoice.
     */
    public function invoiceTax()
    {
        return $this->belongsTo(VatRate::class, 'tax_id');
    }

    /**
     * Get the user who had created this invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SelectedHotelResource);
    }
}