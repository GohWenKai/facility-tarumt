<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Http\Resources\BookingResource;
use App\Services\BookingSearchContext;

class SearchController extends Controller
{
    protected $searchContext;

    public function __construct(BookingSearchContext $searchContext)
    {
        $this->searchContext = $searchContext;
        
        // DELETE OR COMMENT OUT THIS LINE:
        // $this->middleware('throttle:100,1')->only('search'); 
    }

    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with('facility')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.bookings.history', compact('bookings')); 
    }

    public function search(Request $request)
    {
        // ... (rest of your code remains the same)
        $validated = $request->validate([
            'status' => 'nullable|string|in:All,all,pending,approved,rejected', 
            'date'   => 'nullable|date',
        ]);

        $query = $this->searchContext->applyFilters($validated);
        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return BookingResource::collection($bookings);
    }
}