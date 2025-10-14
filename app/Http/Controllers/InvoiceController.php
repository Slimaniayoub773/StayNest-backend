<?php
namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['payment.booking.guest', 'payment.booking.room'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $invoices
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
        ]);

        // Check if invoice already exists for this payment
        $existingInvoice = Invoice::where('payment_id', $validated['payment_id'])->first();
        if ($existingInvoice) {
            return response()->json([
                'success' => false,
                'message' => 'An invoice already exists for this payment'
            ], 422);
        }

        $payment = Payment::with(['booking.guest', 'booking.room'])->find($validated['payment_id']);
        
        $invoice = Invoice::create([
            'payment_id' => $payment->id,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'amount' => $payment->amount,
        ]);

        // Load relationships for response
        $invoice->load(['payment.booking.guest', 'payment.booking.room']);

        return response()->json([
            'success' => true,
            'data' => $invoice
        ], 201);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['payment.booking.guest', 'payment.booking.room']);
        
        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }

    // This endpoint will just return the invoice data for React to generate PDF
    public function download(Invoice $invoice)
    {
        $invoice->load(['payment.booking.guest', 'payment.booking.room']);
        
        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }

    // This endpoint will just return the invoice data for React to view
    public function view(Invoice $invoice)
    {
        $invoice->load(['payment.booking.guest', 'payment.booking.room']);
        
        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }
}