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
}
