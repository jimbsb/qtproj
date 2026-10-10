<?php

use Illuminate\Support\Facades\Route;
use App\Models\Ticket;
Route::get('/', function () {
    return view('base_layout');
});

Route::get('/ticket-management', function () {
    return view('base_layout');
});

Route::get('/api/ticket', function () {
    // $data = [
    //     [
    //         'ticket_id' => 'TK-1024',
    //         'subject'   => ['title' => 'Cannot access dashboard after password reset', 'description' => 'when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white when I am going to dashboard, its all white '],
    //         'status'    => 'open',
    //         'priority'  => 'High',
    //         'assignee'  => 'Alice Smith',
    //         'updated'   => '2 mins ago',
    //     ],
    //     [
    //         'ticket_id' => 'TK-1023',
    //         'subject'   => ['title' => 'Submitted by Michael Chen'],
    //         'status'    => 'in_progress',
    //         'priority'  => 'Medium',
    //         'assignee'  => ['value' => 'David Kim', 'status' => '!bg-amber-200'],
    //         'updated'   => '1 hour ago',
    //     ],
    //     [
    //         'ticket_id' => 'TK-1022',
    //         'subject'   => 'Submitted by Emily R.',
    //         'status'    => 'resolved',
    //         'priority'  => 'Low',
    //         'assignee'  => ['value' => 'Unassigned', 'status' => ''],
    //         'updated'   => 'Yesterday',
    //     ],
    //     [
    //         'ticket_id' => 'TK-1021',
    //         'subject'   => 'Submitted by System Alert',
    //         'status'    => 'open',
    //         'priority'  => 'High',
    //         'assignee'  => '-',
    //         'updated'   => '5 days ago',
    //     ],
    //         [
    //         'ticket_id' => 'TK-1024',
    //         'subject'   => 'Cannot access dashboard after password reset',
    //         'status'    => 'open',
    //         'priority'  => 'High',
    //         'assignee'  => 'Alice Smith',
    //         'updated'   => '2 mins ago',
    //     ],
    //     [
    //         'ticket_id' => 'TK-1023',
    //         'subject'   => 'Submitted by Michael Chen',
    //         'status'    => 'in_progress',
    //         'priority'  => 'Medium',
    //         'assignee'  => ['value' => 'David Kim', 'status' => '!bg-amber-200'],
    //         'updated'   => '1 hour ago',
    //     ],
    //     [
    //         'ticket_id' => 'TK-1022',
    //         'subject'   => 'Submitted by Emily R.',
    //         'status'    => 'resolved',
    //         'priority'  => 'Low',
    //         'assignee'  => ['value' => 'Unassigned'],
    //         'updated'   => 'Yesterday',
    //     ],
    //     [
    //         'ticket_id' => 'TK-1021',
    //         'subject'   => 'Submitted by System Alert',
    //         'status'    => 'open',
    //         'priority'  => 'High',
    //         'assignee'  => '-',
    //         'updated'   => '5 days ago',
    //     ],
    //     [
    //         'ticket_id' => 'TK-1022',
    //         'subject'   => 'Submitted by Emily R.',
    //         'status'    => 'resolved',
    //         'priority'  => 'Low',
    //         'assignee'  => ['value' => 'Unassigned'],
    //         'updated'   => 'Yesterday',
    //     ],
    //     [
    //         'ticket_id' => 'TK-1021',
    //         'subject'   => 'Submitted by System Alert',
    //         'status'    => 'open',
    //         'priority'  => 'High',
    //         'assignee'  => '-',
    //         'updated'   => '5 days ago',
    //     ],
    // ];
    $tickets = Ticket::all();

    return response(['status'=>200,'data'=>$tickets],200)
                    ->header('Content-Type', 'application/json');
});