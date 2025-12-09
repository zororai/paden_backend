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
}
