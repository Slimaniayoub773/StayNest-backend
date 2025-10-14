<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard overview statistics
     */
    public function getOverview()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        return response()->json([
            'totalUsers' => DB::table('users')->where('is_active', true)->count(),
            'totalRooms' => [
                'total' => DB::table('rooms')->count(),
                'available' => DB::table('rooms')->where('status', 'available')->count(),
                'booked' => DB::table('rooms')->where('status', 'booked')->count(),
                'maintenance' => DB::table('rooms')->where('status', 'maintenance')->count(),
            ],
            'totalBookings' => [
                'today' => DB::table('bookings')
                    ->whereDate('created_at', $today)
                    ->count(),
                'thisMonth' => DB::table('bookings')
                    ->where('created_at', '>=', $startOfMonth)
                    ->count(),
                'allTime' => DB::table('bookings')->count(),
            ],
            'totalRevenue' => [
                'overall' => DB::table('payments')
                    ->where('status', 'completed')
                    ->sum('amount'),
                'today' => DB::table('payments')
                    ->where('status', 'completed')
                    ->whereDate('payment_date', $today)
                    ->sum('amount'),
            ],
            'totalOrders' => DB::table('room_service_orders')->count(),
        ]);
    }

    /**
     * Get booking trends data
     */
    public function getBookingTrends($period = 'week')
    {
        $endDate = Carbon::today();
        
        switch ($period) {
            case 'month':
                $startDate = Carbon::today()->subMonth();
                $format = 'Y-m-d';
                $groupBy = 'date';
                break;
            case 'year':
                $startDate = Carbon::today()->subYear();
                $format = 'Y-m';
                $groupBy = 'month';
                break;
            default: // week
                $startDate = Carbon::today()->subWeek();
                $format = 'Y-m-d';
                $groupBy = 'date';
        }

        $bookings = DB::table('bookings')
            ->select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("COUNT(*) as bookings")
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'bookings' => $item->bookings
                ];
            });

        return response()->json($bookings);
    }

    /**
     * Get revenue breakdown data
     */
    public function getRevenueBreakdown($period = 'year')
    {
        $endDate = Carbon::today();
        
        switch ($period) {
            case 'month':
                $startDate = Carbon::today()->subMonth();
                $format = 'Y-m-d';
                $groupBy = 'date';
                break;
            case 'quarter':
                $startDate = Carbon::today()->subQuarter();
                $format = 'Y-m';
                $groupBy = 'month';
                break;
            default: // year
                $startDate = Carbon::today()->subYear();
                $format = 'Y-m';
                $groupBy = 'month';
        }

        $revenue = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                DB::raw("DATE_FORMAT(payments.payment_date, '%Y-%m') as month"),
                DB::raw("SUM(CASE WHEN payments.status = 'completed' THEN payments.amount ELSE 0 END) as total"),
                DB::raw("SUM(CASE WHEN rooms.type_id = 1 THEN payments.amount ELSE 0 END) as stay"), // Adjust type_id as needed
                DB::raw("SUM(CASE WHEN rooms.type_id = 2 THEN payments.amount ELSE 0 END) as service"), // Adjust type_id as needed
                DB::raw("SUM(CASE WHEN bookings.offer_id IS NOT NULL THEN payments.amount ELSE 0 END) as offers")
            )
            ->whereBetween('payments.payment_date', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($revenue);
    }

    /**
     * Get room status data
     */
    public function getRoomStatus()
    {
        $roomStatus = DB::table('rooms')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => ucfirst($item->status),
                    'value' => $item->count
                ];
            });

        return response()->json($roomStatus);
    }

    /**
     * Get top services ordered
     */
    public function getTopServices($limit = 5)
    {
        $topServices = DB::table('room_service_order_items')
            ->join('room_service_items', 'room_service_order_items.item_id', '=', 'room_service_items.id')
            ->select(
                'room_service_items.name_en as name',
                DB::raw('SUM(room_service_order_items.quantity) as orders')
            )
            ->groupBy('room_service_items.id', 'room_service_items.name_en')
            ->orderByDesc('orders')
            ->limit($limit)
            ->get();

        return response()->json($topServices);
    }

    /**
     * Get user growth data
     */
    public function getUserGrowth($period = 'year')
    {
        $endDate = Carbon::today();
        
        switch ($period) {
            case 'month':
                $startDate = Carbon::today()->subMonth();
                $format = 'Y-m-d';
                $groupBy = 'date';
                break;
            case 'quarter':
                $startDate = Carbon::today()->subQuarter();
                $format = 'Y-m';
                $groupBy = 'month';
                break;
            default: // year
                $startDate = Carbon::today()->subYear();
                $format = 'Y-m';
                $groupBy = 'month';
        }

        $userGrowth = DB::table('users')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as date"),
                DB::raw("COUNT(*) as users")
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($userGrowth);
    }

    /**
     * Get ratings distribution
     */
    public function getRatingsDistribution()
    {
        $ratings = DB::table('reviews')
            ->select(
                'rating',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        return response()->json($ratings);
    }

    /**
     * Get last bookings
     */
    public function getLastBookings($limit = 5)
    {
        $bookings = DB::table('bookings')
            ->join('guests', 'bookings.guest_id', '=', 'guests.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->select(
                'bookings.id',
                'guests.name as guest',
                'rooms.room_number as room',
                'bookings.check_in_date as checkIn',
                'bookings.check_out_date as checkOut',
                'bookings.booking_status as status'
            )
            ->orderByDesc('bookings.created_at')
            ->limit($limit)
            ->get();

        return response()->json($bookings);
    }

    /**
     * Get latest payments
     */
    public function getLatestPayments($limit = 5)
    {
        $payments = DB::table('payments')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('guests', 'bookings.guest_id', '=', 'guests.id')
            ->select(
                'payments.id',
                'bookings.id as bookingId',
                'guests.name as guest',
                'payments.amount',
                'payments.payment_method as method',
                'payments.status',
                'payments.payment_date as date'
            )
            ->orderByDesc('payments.payment_date')
            ->limit($limit)
            ->get();

        return response()->json($payments);
    }

    /**
     * Get recent reviews
     */
    public function getRecentReviews($limit = 5)
    {
        $reviews = DB::table('reviews')
            ->join('guests', 'reviews.guest_id', '=', 'guests.id')
            ->join('rooms', 'reviews.room_id', '=', 'rooms.id')
            ->select(
                'reviews.id',
                'guests.name as guest',
                'rooms.room_number as room',
                'reviews.rating',
                'reviews.comment',
                'reviews.created_at as date'
            )
            ->orderByDesc('reviews.created_at')
            ->limit($limit)
            ->get();

        return response()->json($reviews);
    }

    /**
     * Get active rooms list
     */
    public function getActiveRooms()
    {
        $activeRooms = DB::table('rooms')
            ->leftJoin('bookings', function($join) {
                $join->on('rooms.id', '=', 'bookings.room_id')
                    ->where('bookings.booking_status', 'confirmed')
                    ->where('bookings.check_out_date', '>=', Carbon::today());
            })
            ->leftJoin('guests', 'bookings.guest_id', '=', 'guests.id')
            ->select(
                'rooms.room_number as number',
                'room_types.name as type',
                'rooms.status',
                DB::raw("COALESCE(guests.name, '-') as guest"),
                DB::raw("CASE WHEN bookings.check_out_date IS NOT NULL THEN bookings.check_out_date ELSE '-' END as checkOut")
            )
            ->join('room_types', 'rooms.type_id', '=', 'room_types.id')
            ->orderBy('rooms.room_number')
            ->get();

        return response()->json($activeRooms);
    }

    /**
     * Get all dashboard data at once
     */
    public function getAllData()
    {
        return response()->json([
            'overview' => json_decode($this->getOverview()->getContent(), true),
            'bookingTrends' => json_decode($this->getBookingTrends()->getContent(), true),
            'revenueBreakdown' => json_decode($this->getRevenueBreakdown()->getContent(), true),
            'roomStatus' => json_decode($this->getRoomStatus()->getContent(), true),
            'topServices' => json_decode($this->getTopServices()->getContent(), true),
            'userGrowth' => json_decode($this->getUserGrowth()->getContent(), true),
            'ratings' => json_decode($this->getRatingsDistribution()->getContent(), true),
            'lastBookings' => json_decode($this->getLastBookings()->getContent(), true),
            'latestPayments' => json_decode($this->getLatestPayments()->getContent(), true),
            'recentReviews' => json_decode($this->getRecentReviews()->getContent(), true),
            'activeRooms' => json_decode($this->getActiveRooms()->getContent(), true),
        ]);
    }
}