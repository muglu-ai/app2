<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all();
        return view('ticket.index', compact('tickets'));
    }

    public function create()
    {
        return view('ticket.form');
    }

    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('ticket.form', compact('ticket'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_type' => 'nullable|string',
            'nationality' => 'nullable|string',
            'early_bird_date' => 'nullable|date',
            'early_bird_price' => 'nullable|string',
            'normal_price' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        Ticket::create($validated);
        return redirect()->route('tickets.index')->with('success', 'Ticket created');
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'ticket_type' => 'nullable|string',
            'nationality' => 'nullable|string',
            'early_bird_date' => 'nullable|date',
            'early_bird_price' => 'nullable|string',
            'normal_price' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $ticket->update($validated);
        return redirect()->route('tickets.index')->with('success', 'Ticket updated');
    }

    public function destroy($id)
    {
        Ticket::findOrFail($id)->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted');
    }
}
