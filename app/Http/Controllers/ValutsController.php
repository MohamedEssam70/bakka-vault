<?php

namespace App\Http\Controllers;

use App\Models\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class ValutsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the vault with all passwords
     */
    public function index()
    {
        $passwords = Auth::user()->passwords()->orderBy('name')->get();
        return view('content.vault.index', compact('passwords'));
    }

    /**
     * Get password details via AJAX
     */
    public function show(Password $password)
    {
        // Ensure user owns this password
        if ($password->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $password->id,
            'name' => $password->name,
            'username' => $password->username,
            'email' => $password->email,
            'phone' => $password->phone,
            'password' => Crypt::decrypt($password->password),
            'url' => $password->url,
            'notes' => $password->notes,
            'icon' => $password->icon,
            'color' => $password->color,
            'category' => $password->category,
            'tags' => $password->tags ? explode(',', $password->tags) : [],
            'created_at' => $password->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $password->updated_at->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Show the form for creating a new password
     */
    public function create()
    {
        return view('content.vault.create');
    }

    /**
     * Store a newly created password
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:1',
            'url' => 'nullable|url|max:255',
            'notes' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:255'
        ]);

        $password = Password::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Crypt::encrypt($request->password),
            'url' => $request->url,
            'notes' => $request->notes,
            'icon' => $request->icon,
            'color' => $request->color ?: '#007bff',
            'category' => $request->category,
            'tags' => $request->tags,
            'user_id' => Auth::id(),
            'created_by' => Auth::user()->name
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password created successfully',
                'password' => $password
            ]);
        }

        return redirect()->route('vault.index')->with('success', 'Password created successfully');
    }

    /**
     * Show the form for editing the specified password
     */
    public function edit(Password $password)
    {
        // Ensure user owns this password
        if ($password->user_id !== Auth::id()) {
            abort(403);
        }

        return view('content.vault.edit', compact('password'));
    }

    /**
     * Update the specified password
     */
    public function update(Request $request, Password $password)
    {
        // Ensure user owns this password
        if ($password->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:1',
            'url' => 'nullable|url|max:255',
            'notes' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string|max:255'
        ]);

        $password->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Crypt::encrypt($request->password),
            'url' => $request->url,
            'notes' => $request->notes,
            'icon' => $request->icon,
            'color' => $request->color ?: '#007bff',
            'category' => $request->category,
            'tags' => $request->tags,
            'updated_by' => Auth::user()->name
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        }

        return redirect()->route('vault.index')->with('success', 'Password updated successfully');
    }

    /**
     * Remove the specified password
     */
    public function destroy(Password $password)
    {
        // Ensure user owns this password
        if ($password->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $password->update(['deleted_by' => Auth::user()->name]);
        $password->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password deleted successfully'
        ]);
    }

    /**
     * Generate a secure password
     */
    public function generatePassword(Request $request)
    {
        $length = $request->input('length', 12);
        $includeUppercase = $request->input('uppercase', true);
        $includeLowercase = $request->input('lowercase', true);
        $includeNumbers = $request->input('numbers', true);
        $includeSymbols = $request->input('symbols', true);

        $characters = '';
        if ($includeLowercase) $characters .= 'abcdefghijklmnopqrstuvwxyz';
        if ($includeUppercase) $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($includeNumbers) $characters .= '0123456789';
        if ($includeSymbols) $characters .= '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password = '';
        $charactersLength = strlen($characters);
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $charactersLength - 1)];
        }

        return response()->json(['password' => $password]);
    }

    /**
     * Search passwords
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $passwords = Auth::user()->passwords()
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('username', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('url', 'LIKE', "%{$query}%")
                  ->orWhere('category', 'LIKE', "%{$query}%")
                  ->orWhere('tags', 'LIKE', "%{$query}%");
            })
            ->orderBy('name')
            ->get();

        return response()->json($passwords);
    }
}