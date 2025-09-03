<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('items')
            ->latest()
            ->paginate(10);

        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_id' => 'required|string|max:50',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($validated) {
            $invoice = Invoice::create([
                'employee_name' => $validated['employee_name'],
                'employee_id' => $validated['employee_id'],
                'invoice_date' => $validated['invoice_date'],
                'total_amount' => 0, // Will be calculated by the model events
            ]);

            foreach ($validated['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    // amount will be calculated by the model event
                ]);
            }

            // Refresh to get calculated total_amount
            $invoice->load('items');
            
            return response()->json([
                'message' => 'Invoice created successfully',
                'data' => $invoice
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'employee_name' => 'sometimes|required|string|max:255',
            'employee_id' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('invoices', 'employee_id')->ignore($invoice->id)
            ],
            'invoice_date' => 'sometimes|required|date',
            'items' => 'sometimes|required|array|min:1',
            'items.*.id' => 'sometimes|exists:invoice_items,id,invoice_id,' . $invoice->id,
            'items.*.description' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($invoice, $validated) {
            // Update invoice details
            $invoice->fill(collect($validated)
                ->only(['employee_name', 'employee_id', 'invoice_date'])
                ->toArray()
            );

            // Update or create items
            if (isset($validated['items'])) {
                $itemIds = [];
                
                foreach ($validated['items'] as $item) {
                    if (isset($item['id'])) {
                        // Update existing item
                        $invoiceItem = $invoice->items()->findOrFail($item['id']);
                        $invoiceItem->update([
                            'description' => $item['description'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                        ]);
                        $itemIds[] = $invoiceItem->id;
                    } else {
                        // Create new item
                        $newItem = $invoice->items()->create([
                            'description' => $item['description'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                        ]);
                        $itemIds[] = $newItem->id;
                    }
                }

                // Delete items not in the request
                $invoice->items()->whereNotIn('id', $itemIds)->delete();
            }

            $invoice->save();
            $invoice->load('items');

            return response()->json([
                'message' => 'Invoice updated successfully',
                'data' => $invoice
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // The model's deleting event will handle the related items
        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }

    /**
     * Get invoice by invoice_id
     */
    public function getByInvoiceId(string $invoiceId)
    {
        $invoice = Invoice::with('items')
            ->where('invoice_id', $invoiceId)
            ->firstOrFail();
            
        return response()->json($invoice);
    }
}
