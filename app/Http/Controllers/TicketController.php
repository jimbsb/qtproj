<?php

namespace App\Http\Controllers;

use App\Models\fs;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::paginate(15);

        return response(['status'=>200,'data'=>$tickets], 200);
    }

    public function get(Request $request)
    {
        $query = Ticket::query();
        $perPage = $request->per_page;
        $page = $request->page;
        $search = $request->search;


        // Apply search if a search term exists
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_id', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Then paginate the modified query
        $tickets = $query->paginate($perPage ?? 10, ['*'], 'page', $page);

        return response($tickets, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, fs $fs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(fs $fs)
    {
        //
    }
}
