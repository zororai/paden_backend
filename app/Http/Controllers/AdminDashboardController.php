<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        return view('admin.dashboard');
    }

    public function getChartData(Request $request)
    {
        $period = $request->get('period', 'daily'); // daily, weekly, monthly

        $data = [];

        if ($period === 'daily') {
            // Last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $data[] = [
                    'label' => $date->format('M d'),
                    'students' => User::where('type', 'student')
                        ->whereNotNull('email_verified_at')
                        ->whereDate('email_verified_at', $date->format('Y-m-d'))
                        ->count(),
                    'landlords' => User::whereIn('type', ['landlord', 'agent'])
                        ->whereNotNull('email_verified_at')
                        ->whereDate('email_verified_at', $date->format('Y-m-d'))
                        ->count(),
                ];
            }
        } elseif ($period === 'weekly') {
            // Last 8 weeks
            for ($i = 7; $i >= 0; $i--) {
                $startOfWeek = now()->subWeeks($i)->startOfWeek();
                $endOfWeek = now()->subWeeks($i)->endOfWeek();
                $data[] = [
                    'label' => 'Week ' . $startOfWeek->format('M d'),
                    'students' => User::where('type', 'student')
                        ->whereNotNull('email_verified_at')
                        ->whereBetween('email_verified_at', [$startOfWeek, $endOfWeek])
                        ->count(),
                    'landlords' => User::whereIn('type', ['landlord', 'agent'])
                        ->whereNotNull('email_verified_at')
                        ->whereBetween('email_verified_at', [$startOfWeek, $endOfWeek])
                        ->count(),
                ];
            }
        } else {
            // Last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $data[] = [
                    'label' => $date->format('M Y'),
                    'students' => User::where('type', 'student')
                        ->whereNotNull('email_verified_at')
                        ->whereYear('email_verified_at', $date->year)
                        ->whereMonth('email_verified_at', $date->month)
                        ->count(),
                    'landlords' => User::whereIn('type', ['landlord', 'agent'])
                        ->whereNotNull('email_verified_at')
                        ->whereYear('email_verified_at', $date->year)
                        ->whereMonth('email_verified_at', $date->month)
                        ->count(),
                ];
            }
        }

        return response()->json($data);
    }

    public function landlords()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $landlords = User::where('type', 'landlord')
            ->orWhere('type', 'agent')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.landlords', compact('landlords'));
    }

    public function students()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $students = User::where('type', 'student')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.students', compact('students'));
    }

    public function regPayments()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $payments = \App\Models\regMoney::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reg-payments', compact('payments'));
    }

    public function directionPayments()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $payments = \App\Models\Directions::with('user', 'property')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.direction-payments', compact('payments'));
    }

    public function getRegPaymentChartData(Request $request)
    {
        $period = $request->get('period', 'daily');
        $data = [];

        if ($period === 'daily') {
            // Last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $totalAmount = \App\Models\regMoney::whereDate('created_at', $date->toDateString())->sum('amount');
                $count = \App\Models\regMoney::whereDate('created_at', $date->toDateString())->count();
                $data[] = [
                    'label' => $date->format('M d'),
                    'amount' => (float) $totalAmount,
                    'count' => $count,
                ];
            }
        } elseif ($period === 'weekly') {
            // Last 6 weeks
            for ($i = 5; $i >= 0; $i--) {
                $startOfWeek = now()->subWeeks($i)->startOfWeek();
                $endOfWeek = now()->subWeeks($i)->endOfWeek();
                $totalAmount = \App\Models\regMoney::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('amount');
                $count = \App\Models\regMoney::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                $data[] = [
                    'label' => 'Week ' . $startOfWeek->format('M d'),
                    'amount' => (float) $totalAmount,
                    'count' => $count,
                ];
            }
        } elseif ($period === 'monthly') {
            // Last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $totalAmount = \App\Models\regMoney::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');
                $count = \App\Models\regMoney::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $data[] = [
                    'label' => $date->format('M Y'),
                    'amount' => (float) $totalAmount,
                    'count' => $count,
                ];
            }
        } else {
            // Yearly - Last 5 years
            for ($i = 4; $i >= 0; $i--) {
                $year = now()->subYears($i)->year;
                $totalAmount = \App\Models\regMoney::whereYear('created_at', $year)
                    ->sum('amount');
                $count = \App\Models\regMoney::whereYear('created_at', $year)
                    ->count();
                $data[] = [
                    'label' => (string) $year,
                    'amount' => (float) $totalAmount,
                    'count' => $count,
                ];
            }
        }

        return response()->json($data);
    }

    public function getDirectionPaymentChartData(Request $request)
    {
        $period = $request->get('period', 'daily');
        $data = [];

        if ($period === 'daily') {
            // Last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $totalAmount = \App\Models\Directions::whereDate('created_at', $date->toDateString())->sum('amount');
                $count = \App\Models\Directions::whereDate('created_at', $date->toDateString())->count();
                $data[] = [
                    'label' => $date->format('M d'),
                    'amount' => (float) $totalAmount,
                    'count' => $count,
                ];
            }
        } elseif ($period === 'weekly') {
            // Last 6 weeks
            for ($i = 5; $i >= 0; $i--) {
                $startOfWeek = now()->subWeeks($i)->startOfWeek();
                $endOfWeek = now()->subWeeks($i)->endOfWeek();
                $totalAmount = \App\Models\Directions::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('amount');
                $count = \App\Models\Directions::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
                $data[] = [
                    'label' => 'Week ' . $startOfWeek->format('M d'),
                    'amount' => (float) $totalAmount,
                    'count' => $count,
                ];
            }
        } elseif ($period === 'monthly') {
            // Last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $totalAmount = \App\Models\Directions::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');
                $count = \App\Models\Directions::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $data[] = [
                    'label' => $date->format('M Y'),
                    'amount' => (float) $totalAmount,
                    'count' => $count,
                ];
            }
        } else {
            // Yearly - Last 5 years
            for ($i = 4; $i >= 0; $i--) {
                $year = now()->subYears($i)->year;
                $totalAmount = \App\Models\Directions::whereYear('created_at', $year)
                    ->sum('amount');
                $count = \App\Models\Directions::whereYear('created_at', $year)
                    ->count();
                $data[] = [
                    'label' => (string) $year,
                    'amount' => (float) $totalAmount,
                    'count' => $count,
                ];
            }
        }

        return response()->json($data);
    }

    public function regPaymentAnalytics()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        return view('admin.reg-payment-analytics');
    }

    public function directionPaymentAnalytics()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        return view('admin.direction-payment-analytics');
    }

    public function properties()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $properties = \App\Models\Properties::orderBy('created_at', 'desc')->get();
        return view('admin.properties', compact('properties'));
    }

    public function universities()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $universities = \App\Models\University::orderBy('university', 'asc')->get();
        return view('admin.universities', compact('universities'));
    }
}
